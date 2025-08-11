<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use App\Models\Traits\HasImages;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\TelegramBotService;

class Restaurant extends BaseModel
{
    use HasCRUD, HasList, HasSlug, HasImages, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'working_hours',
        'yandex_metrica_code',
        'address',
        'average_receipt',
        'phone',
        'telegram_bot_token',
        'user_id',
        'subtitle',
        'welcome_message'
    ];


    protected $casts = [
        'order' => 'integer',
        'working_hours' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить пользователей, связанных с рестораном (many-to-many)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Добавить пользователя к ресторану с возможностью указания роли
     */
    public function addUser(User $user, ?string $role = null): void
    {
        // Добавляем пользователя к ресторану через many-to-many отношение
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id);
        }

        // Если указана роль, назначаем её пользователю в контексте этого ресторана
        if ($role) {
            $user->assignRestaurantRole($this->id, $role);
        }
    }

    /**
     * Удобный доступ к Telegram сервису в контексте этого ресторана.
     */
    public function bot(): TelegramBotService
    {
        return new TelegramBotService($this->telegram_bot_token);
    }
}
