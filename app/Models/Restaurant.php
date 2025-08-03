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

    /**
     * Устанавливает название, описание и логотип бота в Telegram
     * 
     * @param string|null $name Название бота (по умолчанию название ресторана)
     * @param string|null $description Описание бота (по умолчанию описание ресторана)
     * @param string|null $logoPath Путь к файлу логотипа
     * @return array Результат операций
     * @throws \Exception
     */
    public function setupTelegramBot(?string $name = null, ?string $description = null, ?string $logoPath = null): array
    {
        if (empty($this->telegram_bot_token)) {
            throw new \Exception('Токен Telegram бота не установлен для ресторана ' . $this->name);
        }

        $results = [
            'name' => false,
            'description' => false,
            'photo' => false,
            'errors' => []
        ];

        $httpClient = new HttpClient([
            'timeout' => 30,
            'verify' => false
        ]);

        $baseUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token;

        try {
            // Устанавливаем название бота
            $botName = $name ?? $this->name;
            if (!empty($botName)) {
                $response = $httpClient->post($baseUrl . '/setMyName', [
                    'form_params' => [
                        'name' => $botName
                    ]
                ]);

                $nameResult = json_decode($response->getBody()->getContents(), true);
                $results['name'] = $nameResult['ok'] ?? false;
                
                if (!$results['name']) {
                    $results['errors'][] = 'Ошибка установки названия: ' . ($nameResult['description'] ?? 'Неизвестная ошибка');
                }
            }

            // Устанавливаем описание бота
            $botDescription = $description ?? $this->description;
            if (!empty($botDescription)) {
                $response = $httpClient->post($baseUrl . '/setMyDescription', [
                    'form_params' => [
                        'description' => $botDescription
                    ]
                ]);

                $descResult = json_decode($response->getBody()->getContents(), true);
                $results['description'] = $descResult['ok'] ?? false;
                
                if (!$results['description']) {
                    $results['errors'][] = 'Ошибка установки описания: ' . ($descResult['description'] ?? 'Неизвестная ошибка');
                }
            }

            // Устанавливаем логотип бота (если путь к файлу предоставлен)
            if (!empty($logoPath) && file_exists($logoPath)) {
                $response = $httpClient->post($baseUrl . '/setMyPhoto', [
                    'multipart' => [
                        [
                            'name' => 'photo',
                            'contents' => fopen($logoPath, 'r'),
                            'filename' => basename($logoPath)
                        ]
                    ]
                ]);

                $photoResult = json_decode($response->getBody()->getContents(), true);
                $results['photo'] = $photoResult['ok'] ?? false;
                
                if (!$results['photo']) {
                    $results['errors'][] = 'Ошибка установки логотипа: ' . ($photoResult['description'] ?? 'Неизвестная ошибка');
                }
            }

        } catch (GuzzleException $e) {
            $errorMessage = 'Ошибка HTTP запроса: ' . $e->getMessage();
            $results['errors'][] = $errorMessage;
            Log::error('Telegram Bot Setup Error', [
                'restaurant_id' => $this->id,
                'error' => $errorMessage
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Общая ошибка: ' . $e->getMessage();
            $results['errors'][] = $errorMessage;
            Log::error('Telegram Bot Setup Error', [
                'restaurant_id' => $this->id,
                'error' => $errorMessage
            ]);
        }

        return $results;
    }

    /**
     * Устанавливает логотип бота из загруженных изображений ресторана
     * 
     * @param string|null $imageKey Ключ изображения из коллекции (по умолчанию 'logo')
     * @return array Результат операции
     */
    public function setupTelegramBotLogo(?string $imageKey = 'telegram_avatar'): array
    {
        $results = [
            'photo' => false,
            'errors' => []
        ];

        try {
            // Получаем изображение по ключу
            $image = $this->images()
                ->wherePivot('key', $imageKey ?? 'logo')
                ->orderByPivot('order')
                ->first();
            
            if (!$image) {
                $results['errors'][] = 'Изображение с ключом "' . ($imageKey ?? 'logo') . '" не найдено';
                return $results;
            }

            $logoPath = storage_path('app/public/' . $image->getRawOriginal('url'));
            
            if (!file_exists($logoPath)) {
                $results['errors'][] = 'Файл изображения не найден по пути: ' . $logoPath;
                return $results;
            }

            // Используем основной метод для установки логотипа
            $setupResults = $this->setupTelegramBot(null, null, $logoPath);
            $results['photo'] = $setupResults['photo'];
            $results['errors'] = array_merge($results['errors'], $setupResults['errors']);

        } catch (\Exception $e) {
            $errorMessage = 'Ошибка установки логотипа из медиафайлов: ' . $e->getMessage();
            $results['errors'][] = $errorMessage;
            Log::error('Telegram Bot Logo Setup Error', [
                'restaurant_id' => $this->id,
                'image_key' => $imageKey,
                'error' => $errorMessage
            ]);
        }

        return $results;
    }
}
