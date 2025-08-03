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
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class Restaurant extends BaseModel
{
    use HasCRUD, HasList, HasSlug, HasImages, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        // Вызываем методы настройки Telegram бота после создания ресторана
        static::created(function ($restaurant) {
            if (!empty($restaurant->telegram_bot_token)) {
                try {
                    $restaurant->setupTelegramBot();
                    $restaurant->setupTelegramBotLogo();
                } catch (\Exception $e) {
                    Log::error('Ошибка настройки Telegram бота при создании ресторана', [
                        'restaurant_id' => $restaurant->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });

        // Вызываем методы настройки Telegram бота после обновления ресторана
        static::updated(function ($restaurant) {
            if (!empty($restaurant->telegram_bot_token)) {
                try {
                    $restaurant->setupTelegramBot();
                    $restaurant->setupTelegramBotLogo();
                } catch (\Exception $e) {
                    Log::error('Ошибка настройки Telegram бота при обновлении ресторана', [
                        'restaurant_id' => $restaurant->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        });
    }

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
        'subtitle'
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
}
