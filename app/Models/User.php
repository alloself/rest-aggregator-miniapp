<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\HasImages;
use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes, HasApiTokens, HasImages;

    /**
     * Custom interfaces for ModelTyper - исключаем нежелательные поля
     *
     * @var array
     */
    public array $interfaces = [
        'tokens' => [
            'type' => 'never',
        ],
        'notifications' => [
            'type' => 'never',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'phone',
        'username',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'tokens',           // Скрываем Sanctum tokens от ModelTyper
        'notifications',    // Скрываем notifications от ModelTyper
        'phone',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'phone' => 'hashed',
        ];
    }

    /**
     * Получить рестораны где пользователь имеет роли
     */
    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class);
    }

    /**
     * Получить роли пользователя в конкретном ресторане
     */
    public function getRestaurantRoles(string $restaurantId): array
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();
        
        // Устанавливаем team_id ресторана
        setPermissionsTeamId($restaurantId);
        
        // Получаем роли
        $roles = $this->getRoleNames()->toArray();
        
        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);
        
        return $roles;
    }

    /**
     * Проверить есть ли у пользователя роль в ресторане
     */
    public function hasRestaurantRole(string $restaurantId, string $role): bool
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();
        
        // Устанавливаем team_id ресторана
        setPermissionsTeamId($restaurantId);
        
        // Проверяем роль
        $hasRole = $this->hasRole($role);
        
        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);
        
        return $hasRole;
    }

    /**
     * Назначить роль пользователю в ресторане
     */
    public function assignRestaurantRole(string $restaurantId, string $role): self
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();
        
        // Устанавливаем team_id ресторана
        setPermissionsTeamId($restaurantId);
        
        // Назначаем роль
        $this->assignRole($role);
        
        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);
        
        return $this;
    }

    /**
     * Получить фото профиля пользователя
     */
    public function getProfilePhoto(): ?File
    {
        return $this->images()->wherePivot('key', 'profile_photo')->first();
    }

    /**
     * Получить URL фото профиля пользователя
     */
    public function getProfilePhotoUrl(): ?string
    {
        $photo = $this->getProfilePhoto();
        return $photo ? $photo->url : null;
    }

    /**
     * Проверить есть ли у пользователя фото профиля
     */
    public function hasProfilePhoto(): bool
    {
        return $this->images()->wherePivot('key', 'profile_photo')->exists();
    }

    /**
     * Друзья пользователя (many-to-many self-referencing)
     */
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id')
            ->withPivot(['friend_telegram_id', 'last_photo_file_id', 'last_photo_file_path', 'last_photo_url', 'last_checked_at'])
            ->withTimestamps();
    }

    /**
     * Пользователи, которые добавили текущего пользователя в друзья
     */
    public function friendOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friends', 'friend_id', 'user_id')
            ->withPivot(['friend_telegram_id', 'last_photo_file_id', 'last_photo_file_path', 'last_photo_url', 'last_checked_at'])
            ->withTimestamps();
    }

    /**
     * Добавить друга
     */
    public function addFriend(User $friend, array $telegramData = []): void
    {
        if ($this->id === $friend->id) {
            return; // Нельзя добавлять себя в друзья
        }

        if ($this->friends()->where('friend_id', $friend->id)->exists()) {
            return; // Друг уже добавлен
        }

        // Поддержка старого вызова (telegramData), но пишем только новые поля
        $friendTelegramId = null;
        if (isset($telegramData['friend_telegram_id']) && is_int($telegramData['friend_telegram_id'])) {
            $friendTelegramId = $telegramData['friend_telegram_id'];
        }

        $this->friends()->attach($friend->id, [
            'id' => \Illuminate\Support\Str::uuid(),
            'friend_telegram_id' => $friendTelegramId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Удалить друга
     */
    public function removeFriend(User $friend): void
    {
        $this->friends()->detach($friend->id);
    }

    /**
     * Проверить, является ли пользователь другом
     */
    public function isFriend(User $user): bool
    {
        return $this->friends()->where('friend_id', $user->id)->exists();
    }

    /**
     * Получить URL аватара с учетом Telegram данных
     */
    public function getAvatarUrl(): ?string
    {
        // 1) Если это друг и у него есть telegram_photo_data в pivot таблице — строим динамически через file_id
        if ($this->pivot && isset($this->pivot->telegram_data)) {
            $telegramData = json_decode($this->pivot->telegram_data, true);
            
            if (isset($telegramData['telegram_photo_data']) && is_array($telegramData['telegram_photo_data'])) {
                // Берем фото самого высокого разрешения (последнее в массиве)
                $photos = $telegramData['telegram_photo_data'];
                $highestResPhoto = end($photos);
                
                if (isset($highestResPhoto['file_id'])) {
                    // Строим URL через Telegram Bot API
                    return $this->buildTelegramAvatarUrl($highestResPhoto['file_id']);
                }
            }
        }

        // 2) Пытаемся получить актуальный аватар для самого пользователя через связанные рестораны
        $selfAvatar = $this->buildSelfTelegramAvatarUrl();
        if ($selfAvatar) {
            return $selfAvatar;
        }

        // 3) Фолбэк на сохранённый avatar_url (может быть устаревшим)
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        return null;
    }

    /**
     * Построить URL аватара из Telegram file_id
     */
    private function buildTelegramAvatarUrl(string $fileId): ?string
    {
        try {
            $service = app(\App\Services\TelegramBotService::class);
            $fileInfo = $service->getFile(['file_id' => $fileId]);
            
            if (isset($fileInfo['result']['file_path'])) {
                return $service->getFileUrl($fileInfo['result']['file_path']);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Ошибка получения Telegram аватара', [
                'file_id' => $fileId,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Попробовать построить URL аватара для самого пользователя через привязанные рестораны.
     * Берём chat_id из pivot restaurant_user и используем текущий токен бота ресторана.
     */
    private function buildSelfTelegramAvatarUrl(): ?string
    {
        try {
            // Получаем связанные рестораны вместе с chat_id из pivot
            $restaurants = $this->relationLoaded('restaurants')
                ? $this->getRelation('restaurants')
                : $this->restaurants()->withPivot(['chat_id'])->get();

            foreach ($restaurants as $restaurant) {
                $chatId = isset($restaurant->pivot) ? (string) ($restaurant->pivot->chat_id ?? '') : '';
                $botToken = (string) ($restaurant->telegram_bot_token ?? '');

                if ($chatId === '' || $botToken === '') {
                    continue;
                }

                $service = new \App\Services\TelegramBotService($botToken);

                // Получаем последнее фото профиля
                $photosResponse = $service->getUserProfilePhotos([
                    'user_id' => (int) $chatId,
                    'limit' => 1,
                ]);

                if (!isset($photosResponse['result']['photos'][0])) {
                    continue;
                }

                $firstPhoto = $photosResponse['result']['photos'][0];
                $largestPhoto = is_array($firstPhoto) ? end($firstPhoto) : null;
                if (!is_array($largestPhoto) || !isset($largestPhoto['file_id'])) {
                    continue;
                }

                // Получаем file_path и строим ссылку
                $fileResponse = $service->getFile(['file_id' => (string) $largestPhoto['file_id']]);
                if (isset($fileResponse['result']['file_path'])) {
                    return $service->getFileUrl((string) $fileResponse['result']['file_path']);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Ошибка получения Telegram аватара пользователя (self)', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Получить данные Telegram для друга (из pivot таблицы)
     */
    public function getTelegramData(): ?array
    {
        // Упразднено: оставлено для обратной совместимости, всегда null
        return null;
    }

    /**
     * Accessor для получения полного URL аватара
     */
    public function getFullAvatarUrlAttribute(): ?string
    {
        return $this->getAvatarUrl();
    }

    /**
     * Получить URL аватара пользователя в контексте конкретного ресторана (учитывает токен бота ресторана).
     * Сначала используем данные pivot->telegram_data (для друзей), затем пробуем по chat_id из связи с рестораном.
     */
    public function getAvatarUrlForRestaurant(Restaurant $restaurant): ?string
    {
        // Единая точка получения аватара с кэшированием
        try {
            $avatarService = app(\App\Services\AvatarService::class);
            $url = $avatarService->getFriendAvatarUrlForRestaurant($this, $restaurant);
            if ($url) {
                return $url;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AvatarService недоступен, используем старые фолбэки', [
                'user_id' => $this->id,
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
        }

        // 1) Если в текущей выборке это друг и есть telegram_data — используем file_id
        if ($this->pivot && isset($this->pivot->telegram_data)) {
            try {
                $telegramData = json_decode((string) $this->pivot->telegram_data, true);
                if (is_array($telegramData)) {
                    // 1.1) Прямо из массива размеров фото
                    if (isset($telegramData['telegram_photo_data']) && is_array($telegramData['telegram_photo_data'])) {
                        $photos = $telegramData['telegram_photo_data'];
                        $highestResPhoto = end($photos);
                        if (is_array($highestResPhoto) && isset($highestResPhoto['file_id'])) {
                            $service = $restaurant->bot();
                            $file = $service->getFile(['file_id' => (string) $highestResPhoto['file_id']]);
                            if (isset($file['result']['file_path'])) {
                                return $service->getFileUrl((string) $file['result']['file_path']);
                            }
                        }
                    }

                    // 1.2) Фолбэк на big_file_id/small_file_id из additional_telegram_info.photo
                    if (isset($telegramData['additional_telegram_info']['photo'])) {
                        $photoMeta = $telegramData['additional_telegram_info']['photo'];
                        $candidateIds = [];
                        if (is_array($photoMeta)) {
                            if (isset($photoMeta['big_file_id']) && is_string($photoMeta['big_file_id'])) {
                                $candidateIds[] = $photoMeta['big_file_id'];
                            }
                            if (isset($photoMeta['small_file_id']) && is_string($photoMeta['small_file_id'])) {
                                $candidateIds[] = $photoMeta['small_file_id'];
                            }
                        }

                        foreach ($candidateIds as $candidateId) {
                            try {
                                $service = $restaurant->bot();
                                $file = $service->getFile(['file_id' => $candidateId]);
                                if (isset($file['result']['file_path'])) {
                                    return $service->getFileUrl((string) $file['result']['file_path']);
                                }
                            } catch (\Throwable $eInner) {
                                // Продолжаем к следующему id
                            }
                        }
                    }

                    // 1.3) Если знаем friend_telegram_id — попробуем запросить актуальные фото этим ботом
                    if (isset($telegramData['friend_telegram_id']) && is_int($telegramData['friend_telegram_id'])) {
                        try {
                            $service = $restaurant->bot();
                            $photosResponse = $service->getUserProfilePhotos([
                                'user_id' => $telegramData['friend_telegram_id'],
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
                        } catch (\Throwable $eInner) {
                            // Игнорируем и продолжаем к следующим шагам
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Ошибка получения аватара друга через pivot', [
                    'user_id' => $this->id,
                    'restaurant_id' => $restaurant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 2) Если в avatar_url сохранён file_id — строим ссылку через токен ресторана
        if ($this->avatar_url && !str_starts_with((string) $this->avatar_url, 'http')) {
            try {
                $service = $restaurant->bot();
                $file = $service->getFile(['file_id' => (string) $this->avatar_url]);
                if (isset($file['result']['file_path'])) {
                    return $service->getFileUrl((string) $file['result']['file_path']);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Ошибка получения аватара по file_id из avatar_url (per restaurant)', [
                    'user_id' => $this->id,
                    'restaurant_id' => $restaurant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // 3) Пробуем по chat_id пользователя в этом ресторане
        try {
            $related = $this->relationLoaded('restaurants')
                ? $this->getRelation('restaurants')->firstWhere('id', $restaurant->id)
                : $this->restaurants()->where('restaurant_id', $restaurant->id)->first();

            $chatId = $related && isset($related->pivot) ? (string) ($related->pivot->chat_id ?? '') : '';
            if ($chatId !== '') {
                $service = $restaurant->bot();
                $photos = $service->getUserProfilePhotos(['user_id' => (int) $chatId, 'limit' => 1]);
                if (isset($photos['result']['photos'][0])) {
                    $largest = end($photos['result']['photos'][0]);
                    if (is_array($largest) && isset($largest['file_id'])) {
                        $file = $service->getFile(['file_id' => (string) $largest['file_id']]);
                        if (isset($file['result']['file_path'])) {
                            return $service->getFileUrl((string) $file['result']['file_path']);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Ошибка получения аватара пользователя через restaurant chat_id', [
                'user_id' => $this->id,
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
        }

        // 4) Фолбэк
        return $this->avatar_url ?: null;
    }

    /**
     * Лайки пользователя
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
