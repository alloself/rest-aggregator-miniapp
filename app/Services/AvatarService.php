<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AvatarService
{
    /**
     * Вернуть URL аватара друга для конкретного ресторана.
     * Логика упрощена: стараемся использовать только telegram_id и токен текущего ресторана,
     * кэшируем результат на короткое время. pivot-данные используются как подсказки.
     */
    public function getFriendAvatarUrlForRestaurant(User $friend, Restaurant $restaurant): ?string
    {
        $pivotData = $this->decodePivotTelegramData($friend);

        $friendTelegramId = $this->extractTelegramId($friend, $restaurant, $pivotData);
        if ($friendTelegramId === null) {
            // Фолбэк: если в users.avatar_url уже лежит http — вернём его
            if ($friend->avatar_url && str_starts_with((string) $friend->avatar_url, 'http')) {
                return (string) $friend->avatar_url;
            }
            return null;
        }

        $cacheKey = 'avatar:' . $restaurant->id . ':' . $friendTelegramId;

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($restaurant, $friendTelegramId, $pivotData) {
            try {
                $service = $restaurant->bot();

                // 1) Пытаемся получить актуальные фото пользователя
                $photosResponse = $service->getUserProfilePhotos([
                    'user_id' => (int) $friendTelegramId,
                    'limit' => 1,
                ]);

                if (isset($photosResponse['result']['photos'][0])) {
                    $first = $photosResponse['result']['photos'][0];
                    $largest = is_array($first) ? end($first) : null;
                    if (is_array($largest) && isset($largest['file_id'])) {
                        $file = $service->getFile(['file_id' => (string) $largest['file_id']]);
                        if (isset($file['result']['file_path'])) {
                            return $service->getFileUrl((string) $file['result']['file_path']);
                        }
                    }
                }

                // 2) Фолбэк: попробуем из additional_telegram_info.photo.*
                if ($pivotData && isset($pivotData['additional_telegram_info']['photo']) && is_array($pivotData['additional_telegram_info']['photo'])) {
                    $photo = $pivotData['additional_telegram_info']['photo'];
                    $candidateIds = [];
                    if (isset($photo['big_file_id']) && is_string($photo['big_file_id'])) {
                        $candidateIds[] = $photo['big_file_id'];
                    }
                    if (isset($photo['small_file_id']) && is_string($photo['small_file_id'])) {
                        $candidateIds[] = $photo['small_file_id'];
                    }
                    foreach ($candidateIds as $candidateId) {
                        try {
                            $file = $service->getFile(['file_id' => $candidateId]);
                            if (isset($file['result']['file_path'])) {
                                return $service->getFileUrl((string) $file['result']['file_path']);
                            }
                        } catch (\Throwable $e) {
                            // Пробуем следующий
                        }
                    }
                }

                // 3) Последний шанс: если в pivot лежит массив размеров фото — попытаемся им воспользоваться
                if ($pivotData && isset($pivotData['telegram_photo_data']) && is_array($pivotData['telegram_photo_data'])) {
                    $photos = $pivotData['telegram_photo_data'];
                    $highest = end($photos);
                    if (is_array($highest) && isset($highest['file_id'])) {
                        $file = $service->getFile(['file_id' => (string) $highest['file_id']]);
                        if (isset($file['result']['file_path'])) {
                            return $service->getFileUrl((string) $file['result']['file_path']);
                        }
                    }
                }

            } catch (\Throwable $e) {
                Log::warning('AvatarService: не удалось получить URL аватара', [
                    'restaurant_id' => $restaurant->id,
                    'friend_id' => $friendTelegramId,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
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
}


