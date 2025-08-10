<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\File;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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

        Log::info('🚀 ОТЛАДКА: Telegram Webhook получен', [
            'restaurant_id' => $restaurant->id,
            'update_type' => $this->getUpdateType($update),
            'full_update' => $update,
            'step' => 'webhook_received'
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
        $from = $message['from'] ?? [];

        // Обработка команды /start
        if ($text === '/start' || str_starts_with($text, '/start ')) {
            $this->handleStartCommand($chatId, $service, $restaurant, $text, $from);
            return;
        }

        // Обработка контактных данных
        if (isset($message['contact'])) {
            $this->handleContactMessage($message['contact'], $chatId, $service, $restaurant, $from);
            return;
        }

        // Обработка текстовых сообщений
        if ($text === '⏭️ Пропустить') {
            $this->handleSkipMessage($chatId, $service, $restaurant);
            return;
        }
    }

    /**
     * Обработка команды /start
     */
    private function handleStartCommand(int $chatId, TelegramBotService $service, Restaurant $restaurant, string $text, array $from): void
    {
        // Извлекаем start параметр если есть
        $startParam = null;
        if (str_starts_with($text, '/start ')) {
            $startParam = trim(substr($text, 7));
        }

        // Создаем или обновляем пользователя
        $user = $this->createOrUpdateFromTelegram($from);

        if ($user) {
            Log::info('Пользователь сохранен при открытии Mini App', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Приветственное сообщение
        $greeting = $this->buildWelcomeMessage($restaurant);

        // Отправляем приветственное сообщение БЕЗ клавиатуры
        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => $greeting,
            'parse_mode' => 'HTML',
        ]);

        Log::info('Отправлено приветственное сообщение', [
            'restaurant_id' => $restaurant->id,
            'chat_id' => $chatId,
            'start_param' => $startParam,
            'user_id' => $user?->id,
        ]);

        // Отправляем второе сообщение с предложением поделиться контактами
        $this->sendContactRequestMessage($chatId, $service, $restaurant);
    }

    /**
     * Отправка сообщения с запросом контактных данных
     */
    private function sendContactRequestMessage(int $chatId, TelegramBotService $service, Restaurant $restaurant): void
    {
        $contactMessage = "Поделитесь своими контактами, чтобы мы могли связаться с вами для подтверждения брони и уведомлений о специальных предложениях! 📱";

        // Отправляем сообщение БЕЗ кнопок
        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => $contactMessage,
            'parse_mode' => 'HTML',
        ]);

        // Сразу показываем reply-клавиатуру под полем ввода
        $contactKeyboard = $service->createReplyKeyboard([
            [
                [
                    'text' => '📞 Поделиться контактом',
                    'request_contact' => true,
                ],
            ],
            [
                [
                    'text' => '⏭️ Пропустить',
                ],
            ]
        ], true, false);

        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => '👇 Используйте кнопки ниже:',
            'reply_markup' => $contactKeyboard,
        ]);

        Log::info('Отправлено сообщение с запросом контактов', [
            'restaurant_id' => $restaurant->id,
            'chat_id' => $chatId,
        ]);
    }



    /**
     * Обработка callback query (нажатия inline кнопок)
     */
    private function handleCallbackQuery(array $callbackQuery, TelegramBotService $service, Restaurant $restaurant): void
    {
        $callbackQueryId = $callbackQuery['id'];

        // Отвечаем на любой callback query (на случай старых сообщений)
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

    /**
     * Создать или обновить пользователя по данным из Telegram
     */
    private function createOrUpdateFromTelegram(array $telegramUser): ?User
    {
        try {
            Log::info('🔍 ОТЛАДКА: Начало создания/обновления пользователя', [
                'telegram_user' => $telegramUser,
                'step' => 'start_create_or_update'
            ]);

            $chatId = (string) $telegramUser['id'];
            $firstName = (string) ($telegramUser['first_name'] ?? '');
            $lastName = (string) ($telegramUser['last_name'] ?? '');
            $username = (string) ($telegramUser['username'] ?? '');

            Log::info('🔍 ОТЛАДКА: Извлеченные данные', [
                'chat_id' => $chatId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'step' => 'data_extracted'
            ]);

            if (empty($firstName)) {
                Log::warning('❌ ОТЛАДКА: Отсутствует first_name', [
                    'telegram_user' => $telegramUser,
                    'step' => 'missing_first_name'
                ]);
                return null;
            }

            Log::info('🔍 ОТЛАДКА: Поиск существующего пользователя', [
                'chat_id' => $chatId,
                'step' => 'searching_existing_user'
            ]);

            // Ищем пользователя по chat_id
            $user = User::where('chat_id', $chatId)->first();

            if ($user) {
                Log::info('✅ ОТЛАДКА: Найден существующий пользователь', [
                    'user_id' => $user->id,
                    'chat_id' => $chatId,
                    'step' => 'existing_user_found'
                ]);

                // Обновляем существующего пользователя
                $user->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName ?: null,
                    'username' => $username ?: null,
                ]);

                Log::info('✅ ОТЛАДКА: Пользователь обновлен', [
                    'user_id' => $user->id,
                    'chat_id' => $chatId,
                    'first_name' => $firstName,
                    'step' => 'user_updated'
                ]);

                return $user;
            }

            Log::info('🔍 ОТЛАДКА: Пользователь не найден, создаем нового', [
                'chat_id' => $chatId,
                'step' => 'creating_new_user'
            ]);

            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName ?: null,
                'username' => $username ?: null,
                'chat_id' => $chatId,
            ];

            Log::info('🔍 ОТЛАДКА: Данные для создания пользователя', [
                'user_data' => $userData,
                'step' => 'user_data_prepared'
            ]);

            $user = User::create($userData);

            Log::info('✅ ОТЛАДКА: Новый пользователь создан', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'username' => $username ?: null,
                'first_name' => $firstName,
                'step' => 'new_user_created'
            ]);

            return $user;
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка создания/обновления пользователя', [
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'telegram_user' => $telegramUser,
                'step' => 'error_occurred'
            ]);
            return null;
        }
    }



    /**
     * Обработка сообщения "Пропустить"
     */
    private function handleSkipMessage(int $chatId, TelegramBotService $service, Restaurant $restaurant): void
    {
        // Отправляем подтверждение
        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => '⏭️ Вы пропустили отправку контакта.',
        ]);

        // Заменяем клавиатуру на кнопку приложения
        $this->setAppKeyboard($chatId, $service, $restaurant);

        Log::info('Пользователь пропустил шаг регистрации', [
            'restaurant_id' => $restaurant->id,
            'chat_id' => $chatId,
        ]);
    }


    /**
     * Обработка контактных данных
     */
    private function handleContactMessage(array $contact, int $chatId, TelegramBotService $service, Restaurant $restaurant, array $from): void
    {
        try {
            Log::info('📞 ОТЛАДКА: Начало обработки контакта', [
                'contact' => $contact,
                'chat_id' => $chatId,
                'from' => $from,
                'restaurant_id' => $restaurant->id,
                'step' => 'start_handle_contact'
            ]);

            $phoneNumber = $contact['phone_number'] ?? null;
            $firstName = $contact['first_name'] ?? '';
            $lastName = $contact['last_name'] ?? '';
            $userId = $contact['user_id'] ?? null;

            Log::info('📞 ОТЛАДКА: Извлеченные данные контакта', [
                'phone_number' => $phoneNumber,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_id' => $userId,
                'step' => 'contact_data_extracted'
            ]);

            // Находим пользователя по chat_id
            Log::info('📞 ОТЛАДКА: Поиск пользователя по chat_id', [
                'chat_id' => $chatId,
                'step' => 'searching_user_for_contact'
            ]);

            $user = User::where('chat_id', (string)$chatId)->first();

            if (!$user) {
                Log::error('❌ ОТЛАДКА: Пользователь не найден!', [
                    'chat_id' => $chatId,
                    'step' => 'user_not_found_for_contact'
                ]);

                // Отправляем сообщение об ошибке
                $service->sendMessage([
                    'chat_id' => $chatId,
                    'text' => '❌ Ошибка: пользователь не найден. Попробуйте команду /start.',
                ]);

                return;
            }

            Log::info('✅ ОТЛАДКА: Пользователь найден', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'step' => 'user_found_for_contact'
            ]);

            if (!$phoneNumber) {
                Log::warning('⚠️ ОТЛАДКА: Номер телефона отсутствует', [
                    'contact' => $contact,
                    'step' => 'phone_number_missing'
                ]);

                $service->sendMessage([
                    'chat_id' => $chatId,
                    'text' => '⚠️ Номер телефона не получен. Попробуйте еще раз.',
                ]);

                return;
            }

            Log::info('📞 ОТЛАДКА: Сохранение номера телефона', [
                'user_id' => $user->id,
                'phone_number' => $phoneNumber,
                'step' => 'saving_phone_number'
            ]);

            // Обновляем номер телефона пользователя
            $user->update([
                'phone' => $phoneNumber,
            ]);

            Log::info('✅ ОТЛАДКА: Номер телефона сохранен', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'phone_number' => $phoneNumber,
                'restaurant_id' => $restaurant->id,
                'step' => 'phone_number_saved'
            ]);

            // Отправляем подтверждение
            Log::info('📞 ОТЛАДКА: Отправка подтверждения', [
                'chat_id' => $chatId,
                'step' => 'sending_confirmation'
            ]);

            $service->sendMessage([
                'chat_id' => $chatId,
                'text' => '✅ Спасибо! Контакт сохранен.',
            ]);

            Log::info('📞 ОТЛАДКА: Подтверждение отправлено', [
                'chat_id' => $chatId,
                'step' => 'confirmation_sent'
            ]);

            // Заменяем клавиатуру на кнопку приложения
            Log::info('⌨️ ОТЛАДКА: Замена клавиатуры', [
                'chat_id' => $chatId,
                'step' => 'replacing_keyboard'
            ]);

            $this->setAppKeyboard($chatId, $service, $restaurant);

            Log::info('✅ ОТЛАДКА: Клавиатура заменена', [
                'chat_id' => $chatId,
                'step' => 'keyboard_replaced'
            ]);
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка обработки контактных данных', [
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'chat_id' => $chatId,
                'contact' => $contact,
                'restaurant_id' => $restaurant->id,
                'step' => 'error_in_handle_contact'
            ]);

            // Заменяем клавиатуру даже при ошибке
            $this->setAppKeyboard($chatId, $service, $restaurant);
        }
    }





    /**
     * Установить клавиатуру с кнопкой приложения
     */
    private function setAppKeyboard(int $chatId, TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            Log::info('⌨️ ОТЛАДКА: Начало установки клавиатуры приложения', [
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
                'step' => 'start_set_app_keyboard'
            ]);

            // URL для Mini App
            $webAppUrl = $this->buildWebAppUrl($restaurant);

            Log::info('⌨️ ОТЛАДКА: URL приложения построен', [
                'web_app_url' => $webAppUrl,
                'step' => 'web_app_url_built'
            ]);

            // Создаем клавиатуру с кнопкой для открытия Mini App
            $replyMarkup = $service->createReplyKeyboard([
                [
                    [
                        'text' => 'Открыть приложение',
                        'web_app' => ['url' => $webAppUrl],
                    ],
                ]
            ], true, false);

            Log::info('⌨️ ОТЛАДКА: Клавиатура создана', [
                'reply_markup' => $replyMarkup,
                'step' => 'keyboard_created'
            ]);

            Log::info('⌨️ ОТЛАДКА: Отправка сообщения с новой клавиатурой', [
                'chat_id' => $chatId,
                'step' => 'sending_keyboard_message'
            ]);

            $result = $service->sendMessage([
                'chat_id' => $chatId,
                'text' => '🎉 Готово! Теперь вы можете пользоваться приложением.',
                'reply_markup' => $replyMarkup,
            ]);

            Log::info('✅ ОТЛАДКА: Сообщение с клавиатурой отправлено', [
                'chat_id' => $chatId,
                'result' => $result,
                'step' => 'keyboard_message_sent'
            ]);

            Log::info('✅ ОТЛАДКА: Клавиатура приложения установлена', [
                'restaurant_id' => $restaurant->id,
                'chat_id' => $chatId,
                'step' => 'app_keyboard_set_complete'
            ]);
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка установки клавиатуры приложения', [
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
                'step' => 'error_in_set_app_keyboard'
            ]);
        }
    }

    /**
     * Найти пользователя по chat_id
     */
    private function findByChatId(string $chatId): ?User
    {
        return User::where('chat_id', $chatId)->first();
    }

    /**
     * Проверить есть ли пользователь с данным chat_id
     */
    private function existsByChatId(string $chatId): bool
    {
        return User::where('chat_id', $chatId)->exists();
    }
}
