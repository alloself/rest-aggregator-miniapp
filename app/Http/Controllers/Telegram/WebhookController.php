<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Throwable;

/**
 * Контроллер для обработки webhook запросов от Telegram
 * Обновлен для работы с TelegramBotService v2.0 (API 9.1)
 * 
 * @version 2.0
 */
class WebhookController extends Controller
{
    /**
     * Обработка входящих обновлений от Telegram для конкретного ресторана
     */
    public function handle(Request $request, Restaurant $restaurant)
    {
        $update = $request->all();
        
        Log::info('Telegram Webhook получен', [
            'restaurant_id' => $restaurant->id,
            'update_type' => $this->getUpdateType($update),
        ]);

        try {
            // Проверяем есть ли токен у ресторана
            if (!$restaurant->telegram_bot_token) {
                Log::warning('Webhook получен для ресторана без токена', [
                    'restaurant_id' => $restaurant->id,
                ]);
                return response()->noContent(BaseResponse::HTTP_OK);
            }

            $telegramService = new TelegramBotService($restaurant->telegram_bot_token);
            
            // Обрабатываем различные типы обновлений
            $this->processUpdate($update, $telegramService, $restaurant);

        } catch (Throwable $e) {
            Log::error('Ошибка обработки Telegram webhook', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
                'update' => $update,
            ]);
        }

        return response()->noContent(BaseResponse::HTTP_OK);
    }

    /**
     * Обработка различных типов обновлений
     */
    private function processUpdate(array $update, TelegramBotService $service, Restaurant $restaurant): void
    {
        // Обработка обычных сообщений
        if (isset($update['message'])) {
            $this->handleMessage($update['message'], $service, $restaurant);
        }

        // Обработка callback query (нажатия на inline кнопки)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query'], $service, $restaurant);
        }

        // Обработка inline query
        if (isset($update['inline_query'])) {
            $this->handleInlineQuery($update['inline_query'], $service, $restaurant);
        }

        // Обработка web app query
        if (isset($update['web_app_query'])) {
            $this->handleWebAppQuery($update['web_app_query'], $service, $restaurant);
        }

        // Обработка pre-checkout query (платежи)
        if (isset($update['pre_checkout_query'])) {
            $this->handlePreCheckoutQuery($update['pre_checkout_query'], $service, $restaurant);
        }
    }

    /**
     * Обработка обычных сообщений
     */
    private function handleMessage(array $message, TelegramBotService $service, Restaurant $restaurant): void
    {
        // Обрабатываем только приватные чаты
        if (($message['chat']['type'] ?? '') !== 'private') {
            return;
        }

        $text = (string) ($message['text'] ?? '');
        $chatId = $message['chat']['id'];

        // Обработка команды /start
        if ($text === '/start' || str_starts_with($text, '/start ')) {
            $this->handleStartCommand($chatId, $service, $restaurant, $text);
        }
    }

    /**
     * Обработка команды /start
     */
    private function handleStartCommand(int $chatId, TelegramBotService $service, Restaurant $restaurant, string $text): void
    {
        // Извлекаем start параметр если есть
        $startParam = null;
        if (str_starts_with($text, '/start ')) {
            $startParam = trim(substr($text, 7));
        }

        // Приветственное сообщение
        $greeting = $this->buildWelcomeMessage($restaurant);
        
        // URL для Mini App
        $webAppUrl = $this->buildWebAppUrl($restaurant);
        
        // Создаем клавиатуру с кнопкой для открытия Mini App
        $replyMarkup = $service->createReplyKeyboard([
            [
                [
                    'text' => 'Открыть приложение',
                    'web_app' => ['url' => $webAppUrl],
                ],
            ]
        ], true, false);

        // Отправляем приветственное сообщение
        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => $greeting,
            'reply_markup' => $replyMarkup,
            'parse_mode' => 'HTML',
        ]);

        Log::info('Отправлено приветственное сообщение', [
            'restaurant_id' => $restaurant->id,
            'chat_id' => $chatId,
            'start_param' => $startParam,
        ]);
    }



    /**
     * Обработка callback query (нажатия inline кнопок)
     */
    private function handleCallbackQuery(array $callbackQuery, TelegramBotService $service, Restaurant $restaurant): void
    {
        $callbackQueryId = $callbackQuery['id'];

        // Отвечаем на callback query
        $service->answerCallbackQuery([
            'callback_query_id' => $callbackQueryId,
        ]);
    }

    /**
     * Обработка inline query
     */
    private function handleInlineQuery(array $inlineQuery, TelegramBotService $service, Restaurant $restaurant): void
    {
        $queryId = $inlineQuery['id'];
        $query = $inlineQuery['query'] ?? '';

        $results = [];

        // Создаем результат с информацией о ресторане
        $results[] = [
            'type' => 'article',
            'id' => 'restaurant_info',
            'title' => $restaurant->name,
            'description' => $restaurant->subtitle ?? 'Информация о ресторане',
            'input_message_content' => [
                'message_text' => "🏪 *{$restaurant->name}*\n\n" 
                    . ($restaurant->description ? strip_tags($restaurant->description) : 'Добро пожаловать в наш ресторан!'),
                'parse_mode' => 'Markdown',
            ],
        ];

        $service->answerInlineQuery([
            'inline_query_id' => $queryId,
            'results' => $results,
        ]);
    }

    /**
     * Обработка web app query
     */
    private function handleWebAppQuery(array $webAppQuery, TelegramBotService $service, Restaurant $restaurant): void
    {
        // Обработка запросов от Mini App
        $queryId = $webAppQuery['id'];
        
        // Здесь можно добавить логику обработки данных от Mini App
        // Например, обработка заказов, бронирований и т.д.
        
        Log::info('Получен Web App query', [
            'restaurant_id' => $restaurant->id,
            'query_id' => $queryId,
        ]);
    }

    /**
     * Обработка pre-checkout query (платежи)
     */
    private function handlePreCheckoutQuery(array $preCheckoutQuery, TelegramBotService $service, Restaurant $restaurant): void
    {
        $queryId = $preCheckoutQuery['id'];
        
        // Здесь можно добавить валидацию платежа
        // Пока просто одобряем все платежи
        
        $service->answerPreCheckoutQuery([
            'pre_checkout_query_id' => $queryId,
            'ok' => true,
        ]);

        Log::info('Обработан pre-checkout query', [
            'restaurant_id' => $restaurant->id,
            'query_id' => $queryId,
        ]);
    }

    /**
     * Построение приветственного сообщения
     */
    private function buildWelcomeMessage(Restaurant $restaurant): string
    {
        // Используем фиксированный текст приветствия вместо поля welcome_message
        return "Привет! В этом приложении — всё самое важное о {$restaurant->name}: меню, фото, адрес, актуальные новости, анонсы событий и бронирование.\n\n"
            . "Внутри приложения нажмите кнопку Repeat — это знак, что вы к нам вернетесь. И ваши друзья в Telegram увидят, что вам в {$restaurant->name} действительно понравилось.\n"
            . "А ещё, скоро здесь можно будет оставить комментарий — и вы сможете прочитать отзывы только от ваших знакомых близких людей.\n\n"
            . "🤩 Неужели теперь можно узнать, куда ходят друзья — даже не спрашивая их лично?!";
    }

    /**
     * Построение URL для Mini App
     */
    private function buildWebAppUrl(Restaurant $restaurant): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        return "{$baseUrl}/restaurant/{$restaurant->slug}";
    }



    /**
     * Определение типа обновления для логирования
     */
    private function getUpdateType(array $update): string
    {
        if (isset($update['message'])) {
            return 'message';
        }
        if (isset($update['callback_query'])) {
            return 'callback_query';
        }
        if (isset($update['inline_query'])) {
            return 'inline_query';
        }
        if (isset($update['web_app_query'])) {
            return 'web_app_query';
        }
        if (isset($update['pre_checkout_query'])) {
            return 'pre_checkout_query';
        }
        if (isset($update['shipping_query'])) {
            return 'shipping_query';
        }
        
        return 'unknown';
    }
}
