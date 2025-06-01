<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelegramUser extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'telegram_id',
        'username',
        'first_name',
        'last_name',
        'language_code',
        'is_bot',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'telegram_id' => 'integer',
            'is_bot' => 'boolean',
            'settings' => 'array',
        ]);
    }

    /**
     * Get the API endpoints for this Telegram user.
     */
    public function apiEndpoints(): HasMany
    {
        return $this->hasMany(ApiEndpoint::class, 'user_id');
    }

    /**
     * Get the API calls made by this user.
     */
    public function apiCalls(): HasMany
    {
        return $this->hasMany(ApiCall::class, 'user_id');
    }

    /**
     * Get the user sessions.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }

    /**
     * Find a user by Telegram ID.
     */
    public static function findByTelegramId(int $telegramId): ?static
    {
        return static::where('telegram_id', $telegramId)->first();
    }

    /**
     * Find a user by Telegram ID including soft deleted.
     */
    public static function findByTelegramIdWithTrashed(int $telegramId): ?static
    {
        return static::withTrashed()->where('telegram_id', $telegramId)->first();
    }

    /**
     * Get or create a user by Telegram data.
     */
    public static function createFromTelegram(array $telegramData): static
    {
        return static::updateOrCreate(
            ['telegram_id' => $telegramData['id']],
            [
                'username' => $telegramData['username'] ?? null,
                'first_name' => $telegramData['first_name'] ?? null,
                'last_name' => $telegramData['last_name'] ?? null,
                'language_code' => $telegramData['language_code'] ?? null,
                'is_bot' => $telegramData['is_bot'] ?? false,
            ]
        );
    }
} 