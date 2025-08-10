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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'chat_id',
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
            ->withPivot(['telegram_data'])
            ->withTimestamps();
    }

    /**
     * Пользователи, которые добавили текущего пользователя в друзья
     */
    public function friendOf(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_friends', 'friend_id', 'user_id')
            ->withPivot(['telegram_data'])
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

        $this->friends()->attach($friend->id, [
            'id' => \Illuminate\Support\Str::uuid(),
            'telegram_data' => !empty($telegramData) ? json_encode($telegramData) : null,
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
        // Если есть прямой avatar_url - используем его
        if ($this->avatar_url) {
            return $this->avatar_url;
        }

        // Если это друг и у него есть telegram_photo_data в pivot таблице
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
     * Получить данные Telegram для друга (из pivot таблицы)
     */
    public function getTelegramData(): ?array
    {
        if ($this->pivot && isset($this->pivot->telegram_data)) {
            return json_decode($this->pivot->telegram_data, true);
        }

        return null;
    }

    /**
     * Accessor для получения полного URL аватара
     */
    public function getFullAvatarUrlAttribute(): ?string
    {
        return $this->getAvatarUrl();
    }
}
