<?php

namespace App\Models;

use App\Models\Traits\HasCRUD;
use App\Models\Traits\HasList;
use App\Models\Traits\HasImages;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Models\TelegraphBot;

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






















    /**
     * Получить всех пользователей этого ресторана (включая владельца и сотрудников)
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Получить владельца ресторана
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Получить всех пользователей с их ролями в этом ресторане
     */
    public function getUsersWithRoles()
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();

        // Устанавливаем team_id этого ресторана
        setPermissionsTeamId($this->id);

        // Получаем всех пользователей с их ролями в этом ресторане
        $users = $this->users()->with(['roles' => function ($query) {
            $query->where('restaurant_id', $this->id);
        }])->get();

        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);

        return $users;
    }

    /**
     * Добавить пользователя с ролью в ресторан
     */
    public function addUser(User $user, string $role): void
    {
        // Добавляем связь многие-ко-многим если её нет
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            $this->users()->attach($user->id);
        }

        // Назначаем роль в контексте этого ресторана
        $user->assignRestaurantRole($this->id, $role);
    }

    /**
     * Удалить пользователя из ресторана
     */
    public function removeUser(User $user): void
    {
        // Временно сохраняем текущий team_id
        $currentTeamId = getPermissionsTeamId();

        // Устанавливаем team_id этого ресторана
        setPermissionsTeamId($this->id);

        // Удаляем все роли пользователя в этом ресторане
        $user->syncRoles([]);

        // Восстанавливаем предыдущий team_id
        setPermissionsTeamId($currentTeamId);

        // Удаляем связь многие-ко-многим
        $this->users()->detach($user->id);
    }

    /**
     * Изменить роль пользователя в ресторане
     */
    public function changeUserRole(User $user, string $newRole): void
    {
        $user->assignRestaurantRole($this->id, $newRole);
    }

    /**
     * Настроить Telegram бота для ресторана
     */
    public function setupTelegramBot(): array
    {
        if (!$this->telegram_bot_token) {
            return [
                'success' => false,
                'message' => 'Токен бота не указан'
            ];
        }

        try {
            // Получаем информацию о боте через Telegraph
            $botInfoResponse = Telegraph::bot(['token' => $this->telegram_bot_token])->botInfo()->send();
            
            if (!$botInfoResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'Неверный токен бота'
                ];
            }

            $botInfo = $botInfoResponse->json('result');

            // Подготавливаем описание бота
            $description = "🍽 {$this->name}\n\n";
            if ($this->subtitle) {
                $description .= "{$this->subtitle}\n\n";
            }
            if ($this->description) {
                $description .= $this->description;
            }

            // Устанавливаем команды бота через Telegraph
            Telegraph::bot(['token' => $this->telegram_bot_token])
                ->registerBotCommands([
                    'start' => 'Главное меню ресторана',
                    'menu' => 'Показать меню',
                    'info' => 'Информация о ресторане',
                    'contact' => 'Контакты ресторана'
                ])->send();

            // URL мини-приложения
            $miniAppUrl = config('app.url') . "/restaurant/{$this->slug}/miniapp";

            // Устанавливаем кнопку меню с мини-приложением через Telegraph
            Telegraph::bot(['token' => $this->telegram_bot_token])
                ->setChatMenuButton()
                ->webApp('Открыть меню', $miniAppUrl)
                ->send();

            // Устанавливаем описание и короткое описание бота через собственные методы
            $this->setBotDescription($description);
            $this->setBotShortDescription($this->subtitle ?: "Ресторан {$this->name}");

            // Создаем или обновляем TelegraphBot модель
            $telegraphBot = $this->getTelegraphBot();

            return [
                'success' => true,
                'message' => 'Бот успешно настроен',
                'bot_info' => $botInfo,
                'miniapp_url' => $miniAppUrl,
                'bot_username' => '@' . $botInfo['username'],
                'telegraph_bot_id' => $telegraphBot->id
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Ошибка настройки бота: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Получить или создать TelegraphBot модель для этого ресторана
     */
    public function getTelegraphBot(): ?TelegraphBot
    {
        if (!$this->telegram_bot_token) {
            return null;
        }

        return TelegraphBot::updateOrCreate(
            ['token' => $this->telegram_bot_token],
            ['name' => $this->name . ' Bot']
        );
    }

    /**
     * Проверить, настроен ли Telegram бот
     */
    public function hasTelegramBot(): bool
    {
        return !empty($this->telegram_bot_token);
    }

    /**
     * Установить описание бота (собственная реализация для Telegraph)
     */
    public function setBotDescription(string $description): bool
    {
        if (!$this->telegram_bot_token) {
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->telegram_bot_token}/setMyDescription", [
                'description' => $description
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Ошибка установки описания бота для {$this->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Установить короткое описание бота (собственная реализация для Telegraph)
     */
    public function setBotShortDescription(string $shortDescription): bool
    {
        if (!$this->telegram_bot_token) {
            return false;
        }

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->telegram_bot_token}/setMyShortDescription", [
                'short_description' => $shortDescription
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Ошибка установки короткого описания бота для {$this->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Установить описание чата через Telegraph (для групповых чатов)
     */
    public function setChatDescription(string $description, $chatId = null): bool
    {
        if (!$this->telegram_bot_token) {
            return false;
        }

        try {
            $telegraph = Telegraph::bot(['token' => $this->telegram_bot_token]);
            
            if ($chatId) {
                // Для конкретного чата
                $response = $telegraph->chat($chatId)->setDescription($description)->send();
            } else {
                // Для активного чата
                $response = $telegraph->setDescription($description)->send();
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Ошибка установки описания чата для {$this->name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Получить информацию о боте
     */
    public function getBotInfo(): ?array
    {
        if (!$this->telegram_bot_token) {
            return null;
        }

        try {
            $response = Telegraph::bot(['token' => $this->telegram_bot_token])->botInfo()->send();
            
            if ($response->successful()) {
                return $response->json('result');
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("Ошибка получения информации о боте для {$this->name}: " . $e->getMessage());
            return null;
        }
    }
}
