<?php

namespace App\Services;

use App\Models\File;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    /**
     * Вернуть URL аватара пользователя для конкретного ресторана.
     * Предпочитаем локально сохранённый файл, а Telegram используем как источник для синка.
     */
    public function getAvatarUrlForRestaurant(User $user, Restaurant $restaurant): ?string
    {
        $localAvatarUrl = $user->getProfilePhotoUrl();
        if ($localAvatarUrl) {
            return $localAvatarUrl;
        }

        $storedFileId = $this->extractStoredFileId($user);
        if ($storedFileId !== null) {
            $syncedAvatarUrl = $this->syncUserAvatarFromFileId($user, $restaurant, $storedFileId);
            if ($syncedAvatarUrl) {
                return $syncedAvatarUrl;
            }
        }

        $pivotData = $this->decodePivotTelegramData($user);
        $telegramId = $this->extractTelegramId($user, $restaurant, $pivotData);
        if ($telegramId !== null) {
            $syncedAvatarUrl = $this->syncUserAvatarFromTelegramId($user, $restaurant, $telegramId);
            if ($syncedAvatarUrl) {
                return $syncedAvatarUrl;
            }
        }

        if ($user->avatar_url && str_starts_with((string) $user->avatar_url, 'http')) {
            return (string) $user->avatar_url;
        }

        return null;
    }

    /**
     * Обратная совместимость со старым вызовом из модели User.
     */
    public function getFriendAvatarUrlForRestaurant(User $friend, Restaurant $restaurant): ?string
    {
        return $this->getAvatarUrlForRestaurant($friend, $restaurant);
    }

    /**
     * Получить актуальный file_id по Telegram user_id и синхронизировать локальный avatar.
     */
    public function syncUserAvatarFromTelegramId(User $user, Restaurant $restaurant, int $telegramUserId): ?string
    {
        $cacheKey = 'avatar:file-id:' . $restaurant->id . ':' . $telegramUserId;
        $fileId = Cache::remember($cacheKey, now()->addHours(12), function () use ($restaurant, $telegramUserId) {
            try {
                $service = $restaurant->bot();
                $photosResponse = $service->getUserProfilePhotos([
                    'user_id' => $telegramUserId,
                    'limit' => 1,
                ]);

                if (!isset($photosResponse['result']['photos'][0])) {
                    return null;
                }

                $firstPhoto = $photosResponse['result']['photos'][0];
                $largestPhoto = is_array($firstPhoto) ? end($firstPhoto) : null;

                return is_array($largestPhoto) && isset($largestPhoto['file_id']) && is_string($largestPhoto['file_id'])
                    ? $largestPhoto['file_id']
                    : null;
            } catch (\Throwable $e) {
                Log::warning('AvatarService: не удалось получить file_id аватара по telegram_id', [
                    'restaurant_id' => $restaurant->id,
                    'telegram_user_id' => $telegramUserId,
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        });

        if ($fileId === null) {
            return null;
        }

        if ($user->avatar_url !== $fileId) {
            $user->forceFill(['avatar_url' => $fileId])->save();
        }

        return $this->syncUserAvatarFromFileId($user, $restaurant, $fileId);
    }

    /**
     * Скачать и сохранить локально avatar по Telegram file_id.
     */
    public function syncUserAvatarFromFileId(User $user, Restaurant $restaurant, string $fileId): ?string
    {
        $normalizedFileId = trim($fileId);
        if ($normalizedFileId === '' || str_starts_with($normalizedFileId, 'http')) {
            return null;
        }

        try {
            $service = $restaurant->bot();
            $file = $service->getFile(['file_id' => $normalizedFileId]);

            if (!isset($file['result']['file_path']) || !is_string($file['result']['file_path'])) {
                return null;
            }

            $filePath = $file['result']['file_path'];
            $downloadUrl = $service->getFileUrl($filePath);
            $response = Http::timeout(20)->retry(2, 200)->get($downloadUrl);

            if (!$response->successful()) {
                Log::warning('AvatarService: не удалось скачать avatar из Telegram', [
                    'restaurant_id' => $restaurant->id,
                    'user_id' => $user->id,
                    'file_id' => $normalizedFileId,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $contents = $response->body();
            if ($contents === '') {
                return null;
            }

            if ($user->avatar_url !== $normalizedFileId) {
                $user->forceFill(['avatar_url' => $normalizedFileId])->save();
            }

            return $this->storeProfilePhoto(
                user: $user,
                contents: $contents,
                filePath: $filePath,
                contentType: $response->header('Content-Type'),
            );
        } catch (\Throwable $e) {
            Log::warning('AvatarService: не удалось синхронизировать avatar по file_id', [
                'restaurant_id' => $restaurant->id,
                'user_id' => $user->id,
                'file_id' => $normalizedFileId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Извлечь telegram_id друга: сначала из pivot, затем из привязки к ресторану.
     */
    private function extractTelegramId(User $friend, Restaurant $restaurant, ?array $pivotData): ?int
    {
        if ($pivotData && isset($pivotData['friend_telegram_id']) && is_int($pivotData['friend_telegram_id'])) {
            return $pivotData['friend_telegram_id'];
        }

        try {
            $related = $friend->relationLoaded('restaurants')
                ? $friend->getRelation('restaurants')->firstWhere('id', $restaurant->id)
                : $friend->restaurants()->where('restaurant_id', $restaurant->id)->first();

            $chatId = $related && isset($related->pivot) ? (string) ($related->pivot->chat_id ?? '') : '';
            if ($chatId !== '' && ctype_digit($chatId)) {
                return (int) $chatId;
            }
        } catch (\Throwable) {
            // игнорируем
        }

        return null;
    }

    /**
     * Если в users.avatar_url уже лежит file_id, используем его для локального синка.
     */
    private function extractStoredFileId(User $user): ?string
    {
        $avatarValue = trim((string) ($user->avatar_url ?? ''));

        if ($avatarValue === '' || str_starts_with($avatarValue, 'http')) {
            return null;
        }

        return $avatarValue;
    }

    /**
     * Безопасно декодировать telegram_data из pivot
     */
    private function decodePivotTelegramData(User $friend): ?array
    {
        if ($friend->pivot && isset($friend->pivot->telegram_data)) {
            try {
                $data = json_decode((string) $friend->pivot->telegram_data, true);
                return is_array($data) ? $data : null;
            } catch (\Throwable) {
                return null;
            }
        }
        return null;
    }

    /**
     * Сохранить avatar в локальное хранилище и привязать как profile_photo.
     */
    private function storeProfilePhoto(User $user, string $contents, ?string $filePath, ?string $contentType): ?string
    {
        $extension = $this->resolveExtension($filePath, $contentType);
        $storagePath = 'files/avatars/' . $user->id . '-' . Str::uuid() . '.' . $extension;

        if (!Storage::disk('public')->put($storagePath, $contents)) {
            Log::warning('AvatarService: не удалось записать avatar в локальное хранилище', [
                'user_id' => $user->id,
                'storage_path' => $storagePath,
            ]);

            return null;
        }

        $previousPhotoIds = $user->images()
            ->wherePivot('key', 'profile_photo')
            ->pluck('files.id')
            ->all();

        $newFile = File::create([
            'name' => 'telegram-avatar-' . $user->id . '.' . $extension,
            'url' => $storagePath,
            'extension' => $extension,
        ]);

        if (!empty($previousPhotoIds)) {
            $user->images()->detach($previousPhotoIds);
        }

        $user->images()->attach($newFile->id, [
            'key' => 'profile_photo',
            'order' => 0,
            'type' => 'image',
        ]);

        if (!empty($previousPhotoIds)) {
            $previousPhotos = File::whereIn('id', $previousPhotoIds)->get();

            foreach ($previousPhotos as $previousPhoto) {
                if (!$previousPhoto->fileables()->exists()) {
                    $previousPhoto->deleteEntity();
                }
            }
        }

        return $newFile->url;
    }

    /**
     * Определить расширение файла по file_path или content-type.
     */
    private function resolveExtension(?string $filePath, ?string $contentType): string
    {
        $pathExtension = strtolower((string) pathinfo((string) $filePath, PATHINFO_EXTENSION));
        if ($pathExtension !== '') {
            return $pathExtension;
        }

        $normalizedContentType = strtolower(trim((string) explode(';', (string) $contentType)[0]));

        return match ($normalizedContentType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }
}


