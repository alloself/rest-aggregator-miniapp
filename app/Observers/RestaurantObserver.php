<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Services\TelegramBotService;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Observer для модели Restaurant
 * Настраивает Telegram Bot с нуля при создании/обновлении ресторана
 * 
 * @version 2.0
 */
class RestaurantObserver
{
    /**
     * Полная настройка бота при сохранении ресторана
     * Выполняется полная инициализация бота с нуля
     */
    public function saved(Restaurant $restaurant): void
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            return;
        }
        
        $tokenChanged = true;//$restaurant->wasChanged('telegram_bot_token');
        $wasRecentlyCreated = $restaurant->wasRecentlyCreated;

        Log::info('RestaurantObserver: Начинаем настройку бота', [
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'token_changed' => $tokenChanged,
            'recently_created' => $wasRecentlyCreated,
        ]);

        try {
            $telegramService = new TelegramBotService($token);
            
            // 1. Проверяем доступность API и получаем информацию о боте
            $this->validateBotToken($telegramService, $restaurant);

            // 2. Очищаем старые настройки при смене токена
            if ($tokenChanged && !$wasRecentlyCreated) {
                $this->cleanupOldBotSettings($telegramService);
            }

            // 3. Выполняем полную настройку бота с нуля при создании или смене токена
            $needsFullSetup = $wasRecentlyCreated || $tokenChanged;
            
            if ($needsFullSetup) {
                Log::info('RestaurantObserver: Выполняем полную настройку бота с нуля', [
                    'restaurant_id' => $restaurant->id,
                    'reason' => $wasRecentlyCreated ? 'новая модель' : 'смена токена',
                ]);
                
                $this->performFullBotSetup($telegramService, $restaurant);
            } else {
                // Частичное обновление при изменении других полей
                $this->performPartialBotUpdate($telegramService, $restaurant);
            }

            Log::info('RestaurantObserver: Настройка бота завершена успешно', [
                'restaurant_id' => $restaurant->id,
            ]);

        } catch (Throwable $e) {
            Log::error('RestaurantObserver: Ошибка настройки бота', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            report($e);
        }
    }

    /**
     * Проверка доступности API и валидации токена
     */
    private function validateBotToken(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $botInfo = $service->getMe();
            
            Log::info('RestaurantObserver: Бот успешно найден', [
                'restaurant_id' => $restaurant->id,
                'bot_username' => $botInfo['result']['username'] ?? 'unknown',
                'bot_id' => $botInfo['result']['id'] ?? 'unknown',
            ]);

        } catch (Throwable $e) {
            throw new \RuntimeException("Неверный токен бота или API недоступен: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Очистка старых настроек бота при смене токена
     */
    private function cleanupOldBotSettings(TelegramBotService $service): void
    {
        try {
            Log::info('RestaurantObserver: Начинаем очистку старых настроек бота');
            
            // Удаляем webhook
            $service->deleteWebhook(['drop_pending_updates' => true]);
            Log::debug('RestaurantObserver: Webhook удален');
            
            // Сбрасываем menu button на default
            $service->setChatMenuButton([
                'menu_button' => ['type' => 'default']
            ]);
            Log::debug('RestaurantObserver: Menu button сброшен на default');

            Log::info('RestaurantObserver: Старые настройки бота успешно очищены');

        } catch (Throwable $e) {
            Log::warning('RestaurantObserver: Не удалось очистить старые настройки', [
                'error' => $e->getMessage(),
            ]);
            // Не останавливаем процесс, продолжаем настройку
        }
    }

    /**
     * Полная настройка бота с нуля
     */
    private function performFullBotSetup(TelegramBotService $service, Restaurant $restaurant): void
    {
        // 1. Устанавливаем базовую информацию о боте
        $this->setupBotProfile($service, $restaurant);

        // 2. Настраиваем Mini App
        $this->setupMiniApp($service, $restaurant);

        // 3. Настраиваем Menu Button (web_app)
        $this->setupMenuButton($service, $restaurant);

        // 4. Настраиваем Webhook
        $this->setupWebhook($service, $restaurant);

        // 5. Устанавливаем команды по умолчанию
        $this->setupDefaultCommands($service);
    }

    /**
     * Частичное обновление настроек бота
     */
    private function performPartialBotUpdate(TelegramBotService $service, Restaurant $restaurant): void
    {
        $changedFields = array_keys($restaurant->getDirty());
        
        Log::info('RestaurantObserver: Частичное обновление бота', [
            'restaurant_id' => $restaurant->id,
            'changed_fields' => $changedFields,
        ]);

        // Обновляем профиль бота если изменились связанные поля
        $profileFields = ['name', 'subtitle', 'welcome_message', 'description'];
        if (array_intersect($changedFields, $profileFields)) {
            $this->setupBotProfile($service, $restaurant);
        }

        // Обновляем slug-зависимые компоненты если изменился slug или name
        if (in_array('name', $changedFields) || in_array('slug', $changedFields)) {
            $this->setupMiniApp($service, $restaurant);
            $this->setupMenuButton($service, $restaurant);
            $this->setupWebhook($service, $restaurant);
        }
    }

    /**
     * Настройка профиля бота (имя, описания)
     */
    private function setupBotProfile(TelegramBotService $service, Restaurant $restaurant): void
    {
        // 1. Устанавливаем имя бота
        $botName = $this->prepareBotName($restaurant);
        if ($botName) {
            $service->setMyName(['name' => $botName]);
            Log::info('RestaurantObserver: Установлено имя бота', ['name' => $botName]);
        }

        // 2. Устанавливаем короткое описание
        $shortDescription = $this->prepareShortDescription($restaurant);
        if ($shortDescription) {
            $service->setMyShortDescription(['short_description' => $shortDescription]);
            Log::info('RestaurantObserver: Установлено короткое описание');
        }

        // 3. Устанавливаем полное описание
        $description = $this->prepareLongDescription($restaurant);
        if ($description) {
            $service->setMyDescription(['description' => $description]);
            Log::info('RestaurantObserver: Установлено полное описание');
        }
    }

    /**
     * Подготовка имени бота
     */
    private function prepareBotName(Restaurant $restaurant): ?string
    {
        $name = trim((string) $restaurant->name);
        
        if ($name === '') {
            return null;
        }

        // Telegram ограничивает имя бота 64 символами
        if (mb_strlen($name) > 64) {
            $name = mb_substr($name, 0, 64);
        }

        return $name;
    }

    /**
     * Подготовка короткого описания (до 120 символов)
     */
    private function prepareShortDescription(Restaurant $restaurant): ?string
    {
        $name = trim((string) $restaurant->name);
        if ($name === '') {
            $name = 'нашем ресторане';
        }

        // Короткая версия фиксированного описания
        $short = "Все о {$name}: меню, фото, адрес, новости и бронирование.";

        // Telegram ограничивает короткое описание 120 символами
        if (mb_strlen($short) > 120) {
            $short = mb_substr($short, 0, 117) . '...';
        }

        return $short;
    }

    /**
     * Подготовка полного описания (до 512 символов)
     */
    private function prepareLongDescription(Restaurant $restaurant): ?string
    {
        $name = trim((string) $restaurant->name);
        if ($name === '') {
            $name = 'нашем ресторане';
        }

        // Полная версия фиксированного описания с переносами строк
        $text = "Привет! В этом приложении — все самое важное о {$name}: меню, фото, адрес, актуальные новости, анонсы событий и бронирование.\n\n" .
            "Откройте приложение, чтобы увидеть, сколько друзей поставили Repeat.\n\n" .
            "🖇️ Repeat — это отметка о том, что заведение понравилось и сюда хочется вернуться.\n" .
            "Её видят ваши контакты — удобный способ поделиться своим выбором и узнать, куда ходят друзья.";

        // Ограничение Telegram на 512 символов
        if (mb_strlen($text) > 512) {
            $text = mb_substr($text, 0, 509) . '...';
        }

        return $text;
    }

    /**
     * Настройка Mini App (только логирование - настройка происходит через Menu Button)
     */
    private function setupMiniApp(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $miniAppUrl = $this->buildMiniAppUrl($restaurant);
            
            if (!$miniAppUrl) {
                Log::warning('RestaurantObserver: Не удалось построить URL для Mini App');
                return;
            }

            // В Telegram Bot API нет методов createMainMiniApp/updateMainMiniApp
            // Mini Apps настраиваются через Menu Button (уже настроено в setupMenuButton)
            Log::info('RestaurantObserver: Mini App URL подготовлен', ['url' => $miniAppUrl]);

        } catch (Throwable $e) {
            Log::warning('RestaurantObserver: Ошибка подготовки Mini App URL', [
                'error' => $e->getMessage(),
            ]);
            // Не критично, продолжаем
        }
    }

    /**
     * Построение URL для Mini App
     */
    private function buildMiniAppUrl(Restaurant $restaurant): ?string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $slug = $restaurant->slug;

        if (!$slug) {
            return null;
        }

        return "{$baseUrl}/restaurant/{$slug}";
    }

    /**
     * Настройка кнопки меню чата (web_app) с текстом "Открыть приложение"
     */
    private function setupMenuButton(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $miniAppUrl = $this->buildMiniAppUrl($restaurant);

            if (!$miniAppUrl) {
                Log::warning('RestaurantObserver: Не удалось построить URL для Menu Button');
                return;
            }

            // Требование Telegram: URL должен быть HTTPS
            if (!str_starts_with($miniAppUrl, 'https://')) {
                Log::warning('RestaurantObserver: Mini App URL для Menu Button должен быть HTTPS', [
                    'url' => $miniAppUrl,
                ]);
                return;
            }

            $service->setChatMenuButton([
                'menu_button' => [
                    'type' => 'web_app',
                    'text' => 'Открыть приложение',
                    'web_app' => [
                        'url' => $miniAppUrl,
                    ],
                ],
            ]);

            Log::info('RestaurantObserver: Установлена web_app кнопка меню', [
                'url' => $miniAppUrl,
            ]);
        } catch (Throwable $e) {
            Log::warning('RestaurantObserver: Ошибка установки web_app кнопки меню', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    

    /**
     * Настройка Webhook
     */
    private function setupWebhook(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $webhookUrl = $this->buildWebhookUrl($restaurant);
            
            if (!$webhookUrl) {
                Log::warning('RestaurantObserver: Не удалось построить Webhook URL');
                return;
            }

            // Проверяем, что URL использует HTTPS
            if (!$service->validateWebhookUrl($webhookUrl)) {
                Log::warning('RestaurantObserver: Webhook URL не прошел валидацию', [
                    'url' => $webhookUrl
                ]);
                return;
            }

            $service->setWebhook([
                'url' => $webhookUrl,
                'max_connections' => 40,
                'allowed_updates' => [
                    'message',
                    'callback_query',
                    'inline_query',
                    'chosen_inline_result',
                    'pre_checkout_query',
                    'shipping_query'
                ]
            ]);

            Log::info('RestaurantObserver: Webhook настроен', ['url' => $webhookUrl]);

        } catch (Throwable $e) {
            Log::error('RestaurantObserver: Критическая ошибка настройки Webhook', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
            ]);
            // Webhook критично важен, но не останавливаем весь процесс
        }
    }

    /**
     * Построение URL для Webhook
     */
    private function buildWebhookUrl(Restaurant $restaurant): ?string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        
        // Webhook должен использовать HTTPS
        if (!str_starts_with($baseUrl, 'https://')) {
            Log::warning('RestaurantObserver: APP_URL должен использовать HTTPS для webhook');
            return null;
        }

        return "{$baseUrl}/api/telegram/webhook/{$restaurant->id}";
    }

    /**
     * Настройка команд по умолчанию
     */
    private function setupDefaultCommands(TelegramBotService $service): void
    {
        try {
            $commands = [
                [
                    'command' => 'start',
                    'description' => 'Запустить бота'
                ],
            ];

            $service->setMyCommands(['commands' => $commands]);
            
            Log::info('RestaurantObserver: Команды по умолчанию установлены');

        } catch (Throwable $e) {
            Log::warning('RestaurantObserver: Ошибка установки команд', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Обработка удаления ресторана
     */
    public function deleting(Restaurant $restaurant): void
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            return;
        }

        try {
            $telegramService = new TelegramBotService($token);
            
            // Удаляем webhook
            $telegramService->deleteWebhook(['drop_pending_updates' => true]);
            
            // Сбрасываем настройки бота
            $telegramService->setChatMenuButton([
                'menu_button' => ['type' => 'default']
            ]);

            Log::info('RestaurantObserver: Настройки бота очищены при удалении ресторана', [
                'restaurant_id' => $restaurant->id,
            ]);

        } catch (Throwable $e) {
            Log::warning('RestaurantObserver: Ошибка очистки бота при удалении', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
