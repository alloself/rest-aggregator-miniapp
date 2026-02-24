<?php

namespace App\Services;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Сервис настройки Telegram-бота для ресторана.
 * Выполняет полную настройку (профиль, webhook, menu button, команды) и очистку при удалении.
 */
class TelegramBotSetupService
{
    /**
     * Полная настройка бота: профиль, Mini App, menu button, webhook, команды
     */
    public function setupBot(Restaurant $restaurant): void
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            throw new \RuntimeException('Токен бота не указан');
        }

        $telegramService = new TelegramBotService($token);
        $this->validateBotToken($telegramService, $restaurant);
        $this->performFullBotSetup($telegramService, $restaurant);

        Log::info('TelegramBotSetupService: Настройка бота завершена успешно', [
            'restaurant_id' => $restaurant->id,
        ]);
    }

    /**
     * Очистка настроек бота при удалении ресторана
     */
    public function cleanupBot(Restaurant $restaurant): void
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            return;
        }

        try {
            $telegramService = new TelegramBotService($token);
            $telegramService->deleteWebhook(['drop_pending_updates' => true]);
            $telegramService->setChatMenuButton([
                'menu_button' => ['type' => 'default'],
            ]);

            Log::info('TelegramBotSetupService: Настройки бота очищены при удалении ресторана', [
                'restaurant_id' => $restaurant->id,
            ]);
        } catch (Throwable $e) {
            Log::warning('TelegramBotSetupService: Ошибка очистки бота при удалении', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function validateBotToken(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $botInfo = $service->getMe();
        } catch (Throwable $e) {
            throw new \RuntimeException("Неверный токен бота или API недоступен: {$e->getMessage()}", 0, $e);
        }

        Log::info('TelegramBotSetupService: Бот успешно найден', [
            'restaurant_id' => $restaurant->id,
            'bot_username' => $botInfo['result']['username'] ?? 'unknown',
            'bot_id' => $botInfo['result']['id'] ?? 'unknown',
        ]);
    }

    private function performFullBotSetup(TelegramBotService $service, Restaurant $restaurant): void
    {
        $this->setupBotProfile($service, $restaurant);
        $this->setupMiniApp($service, $restaurant);
        $this->setupMenuButton($service, $restaurant);
        $this->setupWebhook($service, $restaurant);
        $this->setupDefaultCommands($service);
    }

    private function setupBotProfile(TelegramBotService $service, Restaurant $restaurant): void
    {
        $botName = $this->prepareBotName($restaurant);
        if ($botName !== null) {
            $service->setMyName(['name' => $botName]);
            Log::info('TelegramBotSetupService: Установлено имя бота', ['name' => $botName]);
        }

        $shortDescription = $this->prepareShortDescription($restaurant);
        if ($shortDescription !== null) {
            $service->setMyShortDescription(['short_description' => $shortDescription]);
            Log::info('TelegramBotSetupService: Установлено короткое описание');
        }

        $description = $this->prepareLongDescription($restaurant);
        if ($description !== null) {
            $service->setMyDescription(['description' => $description]);
            Log::info('TelegramBotSetupService: Установлено полное описание');
        }
    }

    private function prepareBotName(Restaurant $restaurant): ?string
    {
        $name = trim((string) $restaurant->name);

        if ($name === '') {
            return null;
        }

        if (mb_strlen($name) > 64) {
            $name = mb_substr($name, 0, 64);
        }

        return $name;
    }

    private function prepareShortDescription(Restaurant $restaurant): ?string
    {
        $name = trim((string) $restaurant->name);
        if ($name === '') {
            $name = 'нашем ресторане';
        }

        $short = "Все о {$name}: меню, фото, адрес, новости и бронирование.";

        if (mb_strlen($short) > 120) {
            $short = mb_substr($short, 0, 117) . '...';
        }

        return $short;
    }

    private function prepareLongDescription(Restaurant $restaurant): ?string
    {
        $name = trim((string) $restaurant->name);
        if ($name === '') {
            $name = 'нашем ресторане';
        }

        $text = "В этом приложении — все самое важное о {$name}: меню, фото, адрес, актуальные новости, анонсы событий и бронирование.";

        if (mb_strlen($text) > 512) {
            $text = mb_substr($text, 0, 509) . '...';
        }

        return $text;
    }

    private function setupMiniApp(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $miniAppUrl = $this->buildMiniAppUrl($restaurant);

            if ($miniAppUrl === null) {
                Log::warning('TelegramBotSetupService: Не удалось построить URL для Mini App');
                return;
            }

            Log::info('TelegramBotSetupService: Mini App URL подготовлен', ['url' => $miniAppUrl]);
        } catch (Throwable $e) {
            Log::warning('TelegramBotSetupService: Ошибка подготовки Mini App URL', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildMiniAppUrl(Restaurant $restaurant): ?string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $slug = $restaurant->slug;

        if ($slug === null || $slug === '') {
            return null;
        }

        return "{$baseUrl}/restaurant/{$slug}";
    }

    private function setupMenuButton(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $miniAppUrl = $this->buildMiniAppUrl($restaurant);

            if ($miniAppUrl === null) {
                Log::warning('TelegramBotSetupService: Не удалось построить URL для Menu Button');
                return;
            }

            if (!str_starts_with($miniAppUrl, 'https://')) {
                Log::warning('TelegramBotSetupService: Mini App URL для Menu Button должен быть HTTPS', [
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

            Log::info('TelegramBotSetupService: Установлена web_app кнопка меню', [
                'url' => $miniAppUrl,
            ]);
        } catch (Throwable $e) {
            Log::warning('TelegramBotSetupService: Ошибка установки web_app кнопки меню', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function setupWebhook(TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            $webhookUrl = $this->buildWebhookUrl($restaurant);

            if ($webhookUrl === null) {
                Log::warning('TelegramBotSetupService: Не удалось построить Webhook URL');
                return;
            }

            if (!$service->validateWebhookUrl($webhookUrl)) {
                Log::warning('TelegramBotSetupService: Webhook URL не прошел валидацию', [
                    'url' => $webhookUrl,
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
                    'shipping_query',
                ],
            ]);

            Log::info('TelegramBotSetupService: Webhook настроен', ['url' => $webhookUrl]);
        } catch (Throwable $e) {
            Log::error('TelegramBotSetupService: Критическая ошибка настройки Webhook', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
            ]);
        }
    }

    private function buildWebhookUrl(Restaurant $restaurant): ?string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        if (!str_starts_with($baseUrl, 'https://')) {
            Log::warning('TelegramBotSetupService: APP_URL должен использовать HTTPS для webhook');
            return null;
        }

        return "{$baseUrl}/api/telegram/webhook/{$restaurant->id}";
    }

    private function setupDefaultCommands(TelegramBotService $service): void
    {
        try {
            $commands = [
                [
                    'command' => 'start',
                    'description' => 'Запустить бота',
                ],
            ];

            $service->setMyCommands(['commands' => $commands]);

            Log::info('TelegramBotSetupService: Команды по умолчанию установлены');
        } catch (Throwable $e) {
            Log::warning('TelegramBotSetupService: Ошибка установки команд', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
