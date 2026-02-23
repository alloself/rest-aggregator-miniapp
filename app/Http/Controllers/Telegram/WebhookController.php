<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Throwable;

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
        $chatType = (string) ($message['chat']['type'] ?? 'private');
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

        // Обработка переданных пользователей (адресная книга)
        if (isset($message['users_shared'])) {
            $this->handleUsersSharedMessage($message['users_shared'], $chatId, $service, $restaurant, $from);
            return;
        }

        // Обработка команды /contacts
        if ($text === '/contacts') {
            $this->sendContactRequestMessage($chatId, $service, $restaurant);
            return;
        }

        // Обработка запроса на создание ссылки-приглашения
        if ($text === 'Пригласить друга') {
            $this->handleCreateInviteLink($chatId, $service, $restaurant);
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

        // Создаем или обновляем пользователя (без chat_id в users)
        $user = $this->createOrUpdateFromTelegram($from, $service, $restaurant);

        if ($user) {
            Log::info('Пользователь сохранен при открытии Mini App', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'restaurant_id' => $restaurant->id,
            ]);

            // Привязываем пользователя к ресторану и назначаем роль telegram_user, если ещё не привязан
            $restaurant->addUser($user, 'telegram_user');
            // Обновляем chat_id/chat_type в пивоте restaurant_user
            $restaurant->users()->updateExistingPivot($user->id, [
                'chat_id' => (string) $chatId,
                'chat_type' => 'private',
                'updated_at' => now(),
            ]);
        }

        // Обработка deep-link приглашения (формат: r{restaurant_uuid}-i{inviter_chat_id})
        $invitedByText = '';
        if (!empty($startParam)) {
            if (preg_match('/^r([0-9a-f\-]+)-i(\d+)$/i', (string)$startParam, $matches) === 1) {
                $refRestaurantId = (string) ($matches[1] ?? '');
                $inviterChatId = (string) ($matches[2] ?? '');

                if ($refRestaurantId === (string) $restaurant->id && $user) {
                    $inviter = User::whereHas('restaurants', function ($q) use ($restaurant, $inviterChatId) {
                        $q->where('restaurant_id', $restaurant->id)
                            ->where('chat_id', (string)$inviterChatId);
                    })->first();

                    if ($inviter && $inviter->id !== $user->id) {
                        // Проверяем, был ли этот пользователь уже в друзьях у приглашавшего
                        $alreadyFriend = $inviter->isFriend($user);

                        // Сохраняем только friend_telegram_id в pivot
                        $inviter->addFriend($user, [
                            'friend_telegram_id' => (int) $chatId,
                        ]);

                        $inviterDisplayName = !empty($inviter->username)
                            ? '@' . $inviter->username
                            : trim((string)$inviter->first_name . ($inviter->last_name ? ' ' . (string)$inviter->last_name : ''));
                        if ($inviterDisplayName !== '') {
                            $invitedByText = "Вас пригласил(а) {$inviterDisplayName}.";
                        }

                        // Уведомляем приглашавшего только один раз — при первом присоединении друга по инвайту
                        if (!$alreadyFriend) {
                            try {
                                $joinedDisplayName = !empty($user->username)
                                    ? '@' . $user->username
                                    : trim((string)$user->first_name . ($user->last_name ? ' ' . (string)$user->last_name : ''));
                                if ($joinedDisplayName === '') {
                                    $joinedDisplayName = 'Ваш друг';
                                }
                                $inviterNotificationLines = [
                                    "✅ {$joinedDisplayName} присоединился(ась) к Eat.Drink.Repeat! Теперь вы можете видеть отметки Repeat друг друга во всех ресторанах платформы.",
                                    '',
                                    '📋 Ваши друзья в Eat.Drink.Repeat:',
                                ];
                                $friends = $inviter->friends()->orderBy('users.first_name')->get();
                                foreach ($friends as $index => $friend) {
                                    $friendDisplay = !empty($friend->username) ? '@' . $friend->username : trim($friend->first_name . ($friend->last_name ? ' ' . $friend->last_name : ''));
                                    $inviterNotificationLines[] = '      ' . ($index + 1) . '. ' . $friendDisplay;
                                }
                                $service->sendMessage([
                                    'chat_id' => (int)$inviterChatId,
                                    'text' => implode("\n", $inviterNotificationLines),
                                ]);
                            } catch (\Throwable $e) {
                                Log::warning('Не удалось уведомить пригласившего о присоединении друга', [
                                    'error' => $e->getMessage(),
                                    'inviter_chat_id' => $inviterChatId,
                                    'restaurant_id' => $restaurant->id,
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Приветственное сообщение
        $greeting = $this->buildWelcomeMessage($restaurant, $invitedByText !== '');
        if ($invitedByText !== '') {
            $greeting = implode("\n\n", [$invitedByText, $greeting]);
        }

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

        // Отправляем второе сообщение с предложением поделиться контактами (и при /start, и при переходе по ссылке)
        $this->sendContactRequestMessage($chatId, $service, $restaurant);
    }

    /**
     * Отправка сообщения с запросом контактных данных
     */
    private function sendContactRequestMessage(int $chatId, TelegramBotService $service, Restaurant $restaurant): void
    {
        $contactMessage = implode("\n", [
            '👥 Пригласите друзей, чтобы видеть отметки Repeat друг друга.',
            '',
            'Это нужно сделать один раз — платформа Eat.Drink.Repeat объединяет друзей во всех подключенных ресторанах.',
        ]);

        // Отправляем сообщение БЕЗ кнопок
        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => $contactMessage,
            'parse_mode' => 'HTML',
        ]);

        // Сразу показываем reply-клавиатуру под полем ввода
        $contactKeyboardButtons = [];

        // Кнопка поделиться контактами показывается только если у пользователя ещё нет телефона
        $userForContact = User::whereHas('restaurants', function ($q) use ($restaurant, $chatId) {
            $q->where('restaurant_id', $restaurant->id)
                ->where('chat_id', (string)$chatId);
        })->first();

        if (!$userForContact || empty($userForContact->phone)) {
            $contactKeyboardButtons[] = [
                [
                    'text' => '📞 Поделиться контактом',
                    'request_contact' => true,
                ],
            ];
        }

        $contactKeyboardButtons[] = [
            [
                'text' => '👥 Поделиться друзьями',
                'request_users' => [
                    'request_id' => 1,
                    'user_is_bot' => false,
                    'max_quantity' => 10,
                    'request_name' => true,
                    'request_username' => true,
                    'request_photo' => true,
                ],
            ],
        ];

        // Кнопка создания ссылки-приглашения
        $contactKeyboardButtons[] = [
            [
                'text' => 'Пригласить друга',
            ],
        ];

        $contactKeyboardButtons[] = [
            [
                'text' => '⏭️ Пропустить',
            ],
        ];

        $contactKeyboard = $service->createReplyKeyboard($contactKeyboardButtons, true, false);

        $service->sendMessage([
            'chat_id' => $chatId,
            'text' => '👇 Используйте кнопку ниже:',
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
        $descriptionText = $restaurant->description ? strip_tags($restaurant->description) : 'Добро пожаловать в наш ресторан!';
        $inlineMessageText = implode("\n", [
            "🏪 *{$restaurant->name}*",
            '',
            $descriptionText,
        ]);
        $results[] = [
            'type' => 'article',
            'id' => 'restaurant_info',
            'title' => $restaurant->name,
            'description' => $restaurant->subtitle ?? 'Информация о ресторане',
            'input_message_content' => [
                'message_text' => $inlineMessageText,
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
     *
     * @param  bool  $openAppWithPeriod  true — для приглашённых («Откройте приложение.»)
     */
    private function buildWelcomeMessage(Restaurant $restaurant, bool $openAppWithPeriod = false): string
    {
        $openAppText = $openAppWithPeriod ? 'Откройте приложение.' : 'Откройте приложение';
        $privacyText = "Продолжая использование чат-бота и Mini App ресторана {$restaurant->name}, вы подтверждаете согласие на обработку ваших персональных данных в соответствии с условиями политики конфиденциальности.";

        return implode("\n", [
            'Привет!',
            "В приложении собрана вся информация о {$restaurant->name}: меню, фото, адрес, новости и бронирование.",
            '',
            'Здесь можно поставить «Repeat», если вы планируете вернуться сюда ещё раз. Отметку увидят ваши друзья — так они узнают, какие места вы рекомендуете.',
            '',
            $openAppText,
            '',
            $privacyText,
        ]) . "\n";
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
     * Получить дополнительную информацию о пользователе через getChat
     */
    private function getUserInfo(int $userId, TelegramBotService $service): ?array
    {
        try {
            Log::info('👤 ОТЛАДКА: Начало получения информации о пользователе', [
                'user_id' => $userId,
                'step' => 'start_get_user_info'
            ]);

            $response = $service->getChat([
                'chat_id' => $userId,
            ]);

            Log::info('👤 ОТЛАДКА: Ответ getChat', [
                'response' => $response,
                'step' => 'got_chat_response'
            ]);

            if (isset($response['result'])) {
                return $response['result'];
            }

            return null;
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка получения информации о пользователе', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'step' => 'error_getting_user_info'
            ]);
            return null;
        }
    }

    /**
     * Получить URL аватара пользователя из Telegram
     */
    private function getUserAvatarUrl(int $userId, TelegramBotService $service): ?string
    {
        try {
            Log::info('🖼️ ОТЛАДКА: Начало получения аватара пользователя', [
                'user_id' => $userId,
                'step' => 'start_get_avatar'
            ]);

            // Получаем фотографии профиля пользователя
            $response = $service->getUserProfilePhotos([
                'user_id' => $userId,
                'limit' => 1,
            ]);

            Log::info('🖼️ ОТЛАДКА: Ответ getUserProfilePhotos', [
                'response' => $response,
                'step' => 'got_profile_photos_response'
            ]);

            if (!isset($response['result']['photos']) || empty($response['result']['photos'])) {
                Log::info('🖼️ ОТЛАДКА: У пользователя нет фотографий профиля', [
                    'user_id' => $userId,
                    'step' => 'no_profile_photos'
                ]);
                return null;
            }

            // Берем первую (последнюю загруженную) фотографию
            $firstPhoto = $response['result']['photos'][0];

            if (empty($firstPhoto)) {
                Log::info('🖼️ ОТЛАДКА: Первая фотография пустая', [
                    'user_id' => $userId,
                    'step' => 'first_photo_empty'
                ]);
                return null;
            }

            // Берем наибольшее разрешение (последний элемент в массиве)
            $largestPhoto = end($firstPhoto);

            if (!isset($largestPhoto['file_id'])) {
                Log::warning('🖼️ ОТЛАДКА: Отсутствует file_id в фотографии', [
                    'largest_photo' => $largestPhoto,
                    'step' => 'missing_file_id'
                ]);
                return null;
            }

            $fileId = $largestPhoto['file_id'];

            Log::info('🖼️ ОТЛАДКА: Получен file_id', [
                'file_id' => $fileId,
                'step' => 'got_file_id'
            ]);

            // Получаем информацию о файле
            $fileResponse = $service->getFile([
                'file_id' => $fileId,
            ]);

            Log::info('🖼️ ОТЛАДКА: Ответ getFile', [
                'file_response' => $fileResponse,
                'step' => 'got_file_response'
            ]);

            if (!isset($fileResponse['result']['file_path'])) {
                Log::warning('🖼️ ОТЛАДКА: Отсутствует file_path в ответе', [
                    'file_response' => $fileResponse,
                    'step' => 'missing_file_path'
                ]);
                return null;
            }

            $filePath = $fileResponse['result']['file_path'];

            // Строим полный URL к файлу
            $avatarUrl = $service->getFileUrl($filePath);

            Log::info('✅ ОТЛАДКА: Получен URL аватара', [
                'avatar_url' => $avatarUrl,
                'user_id' => $userId,
                'step' => 'got_avatar_url'
            ]);

            return $avatarUrl;
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка получения аватара пользователя', [
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'user_id' => $userId,
                'step' => 'error_getting_avatar'
            ]);
            return null;
        }
    }

    /**
     * Создать или обновить пользователя по данным из Telegram
     */
    private function createOrUpdateFromTelegram(array $telegramUser, TelegramBotService $service, ?Restaurant $restaurant = null): ?User
    {
        try {
            Log::info('🔍 ОТЛАДКА: Начало создания/обновления пользователя', [
                'telegram_user' => $telegramUser,
                'step' => 'start_create_or_update'
            ]);

            $chatId = (string) $telegramUser['id'];
            $userId = (int) $telegramUser['id'];
            $firstName = (string) ($telegramUser['first_name'] ?? '');
            $lastName = (string) ($telegramUser['last_name'] ?? '');
            $username = (string) ($telegramUser['username'] ?? '');

            // Получаем дополнительную информацию о пользователе
            $userInfo = $this->getUserInfo($userId, $service);
            $avatarUrl = $this->getUserAvatarUrl($userId, $service);

            // Обновляем данные из дополнительной информации если доступно
            if ($userInfo) {
                // Используем bio из getChat как дополнительную информацию
                if (isset($userInfo['bio'])) {
                    Log::info('👤 ОТЛАДКА: Получена дополнительная информация', [
                        'bio' => $userInfo['bio'],
                        'user_id' => $userId,
                        'step' => 'got_additional_user_info'
                    ]);
                }
            }

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

            // Ищем пользователя по chat_id в pivot для текущего ресторана
            $user = User::whereHas('restaurants', function ($q) use ($chatId, $restaurant) {
                $q->where('restaurant_id', $restaurant->id)
                    ->where('chat_id', (string)$chatId);
            })->first();

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
                    'avatar_url' => $avatarUrl,
                ]);

                Log::info('✅ ОТЛАДКА: Пользователь обновлен', [
                    'user_id' => $user->id,
                    'chat_id' => $chatId,
                    'first_name' => $firstName,
                    'step' => 'user_updated'
                ]);

                return $user;
            }

            // Пропускаем глобальный поиск по users.chat_id — колонка удалена; чат хранится в restaurant_user

            // Попытка сопоставить с существующим пользователем по username или по имени/фамилии,
            // чтобы не создавать дубль, если пользователь был создан ранее через приглашение
            $matched = null;
            if ($username !== '') {
                $matched = User::where('username', $username)->first();
            }
            if (!$matched && $firstName !== '') {
                $q = User::query()->where('first_name', $firstName);
                if ($lastName !== '') {
                    $q->where('last_name', $lastName);
                }
                $matched = $q->first();
            }

            if ($matched) {
                $matched->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName ?: $matched->last_name,
                    'username' => $username !== '' ? $username : $matched->username,
                    'avatar_url' => $avatarUrl ?: $matched->avatar_url,
                ]);

                Log::info('🔁 ОТЛАДКА: Сопоставлен существующий пользователь по профилю', [
                    'user_id' => $matched->id,
                    'chat_id' => $chatId,
                    'matched_by' => $username !== '' ? 'username' : 'name',
                    'step' => 'matched_existing_by_profile'
                ]);

                return $matched;
            }

            Log::info('🔍 ОТЛАДКА: Пользователь не найден нигде, создаем нового', [
                'chat_id' => $chatId,
                'step' => 'creating_new_user'
            ]);

            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName ?: null,
                'username' => $username ?: null,
                'avatar_url' => $avatarUrl,
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
     * Обработка переданных пользователей (адресная книга)
     */
    private function handleUsersSharedMessage(array $usersShared, int $chatId, TelegramBotService $service, Restaurant $restaurant, array $from): void
    {
        try {
            Log::info('👥 ОТЛАДКА: Начало обработки переданных пользователей', [
                'users_shared' => $usersShared,
                'chat_id' => $chatId,
                'from' => $from,
                'restaurant_id' => $restaurant->id,
                'step' => 'start_handle_users_shared'
            ]);

            $requestId = $usersShared['request_id'] ?? null;
            $users = $usersShared['users'] ?? [];

            Log::info('👥 ОТЛАДКА: Извлеченные данные переданных пользователей', [
                'request_id' => $requestId,
                'users_count' => count($users),
                'users' => $users,
                'step' => 'users_shared_data_extracted'
            ]);

            // Находим пользователя по chat_id
            $user = User::whereHas('restaurants', function ($q) use ($chatId, $restaurant) {
                $q->where('restaurant_id', $restaurant->id)
                    ->where('chat_id', (string)$chatId);
            })->first();

            if (!$user) {
                Log::error('❌ ОТЛАДКА: Пользователь не найден!', [
                    'chat_id' => $chatId,
                    'step' => 'user_not_found_for_users_shared'
                ]);

                $service->sendMessage([
                    'chat_id' => $chatId,
                    'text' => '❌ Ошибка: пользователь не найден. Попробуйте команду /start.',
                ]);

                return;
            }

            // Обрабатываем и сохраняем каждого переданного пользователя
            $savedFriendsCount = 0;
            foreach ($users as $index => $sharedUser) {
                Log::info('👤 ОТЛАДКА: Информация о переданном пользователе', [
                    'index' => $index,
                    'user_id' => $sharedUser['user_id'] ?? null,
                    'first_name' => $sharedUser['first_name'] ?? null,
                    'last_name' => $sharedUser['last_name'] ?? null,
                    'username' => $sharedUser['username'] ?? null,
                    'photo' => $sharedUser['photo'] ?? null,
                    'step' => 'shared_user_details'
                ]);

                // Получаем дополнительную информацию о пользователе
                $userId = null;
                $userInfo = null;
                $avatarUrl = null;

                if (isset($sharedUser['user_id'])) {
                    $userId = (int) $sharedUser['user_id'];

                    // Больше не сохраняем лишние данные, используем только friend_telegram_id

                    // Если этот друг уже есть у пользователя (по username/имени), не создаём дубликат
                    $existingFriend = null;
                    if (!empty($sharedUser['username'])) {
                        $existingFriend = $user->friends()->where('users.username', (string)$sharedUser['username'])->first();
                    }
                    if (!$existingFriend && !empty($sharedUser['first_name'])) {
                        $existingFriend = $user->friends()
                            ->where('users.first_name', (string)$sharedUser['first_name'])
                            ->when(isset($sharedUser['last_name']) && $sharedUser['last_name'] !== '', function ($q) use ($sharedUser) {
                                $q->where('users.last_name', (string)$sharedUser['last_name']);
                            })
                            ->first();
                    }

                    $friendUser = $existingFriend ?: $this->findOrCreateFriendUser($userId, $sharedUser, $userInfo, $avatarUrl, $restaurant);

                    if ($friendUser) {
                        // Если уже есть связь user->friend, не создаём дубликат
                        if ($user->friends()->where('friend_id', $friendUser->id)->exists()) {
                            Log::info('ℹ️ ОТЛАДКА: Друг уже привязан к пользователю, пропускаем attach', [
                                'user_id' => $user->id,
                                'friend_user_id' => $friendUser->id,
                                'friend_telegram_id' => $userId,
                                'step' => 'friend_already_attached'
                            ]);
                            continue;
                        }
                        // Добавляем друга к текущему пользователю
                        try {
                            // Сохраняем только friend_telegram_id в pivot
                            $user->addFriend($friendUser, [
                                'friend_telegram_id' => $userId,
                            ]);
                            $savedFriendsCount++;

                            Log::info('✅ ОТЛАДКА: Друг успешно добавлен', [
                                'user_id' => $user->id,
                                'friend_user_id' => $friendUser->id,
                                'friend_telegram_id' => $userId,
                                'friend_name' => ($sharedUser['first_name'] ?? '') . ' ' . ($sharedUser['last_name'] ?? ''),
                                'step' => 'friend_successfully_added'
                            ]);

                            // Пытаемся отправить приветственное сообщение приглашенному пользователю
                            $this->tryGreetInvitedUser(
                                invitedTelegramId: $userId,
                                inviterChatId: (int) $chatId,
                                inviterName: trim(($user->first_name ?? '') . (isset($user->last_name) ? ' ' . $user->last_name : '')),
                                restaurant: $restaurant,
                                service: $service,
                                invitedDisplayName: trim(($sharedUser['first_name'] ?? '') . (isset($sharedUser['last_name']) ? ' ' . $sharedUser['last_name'] : ''))
                            );
                        } catch (Throwable $e) {
                            Log::error('❌ ОТЛАДКА: Ошибка добавления друга', [
                                'error' => $e->getMessage(),
                                'user_id' => $user->id,
                                'friend_telegram_id' => $userId,
                                'step' => 'error_adding_friend'
                            ]);
                        }
                    }
                }
            }

            // Отправляем подтверждение
            $usersCount = count($users);
            $friendsWord = $this->pluralizeRussian($usersCount, 'друге', 'друзьях', 'друзьях');
            $lines = [
                "✅ Спасибо! Получена информация о {$usersCount} {$friendsWord} из вашей адресной книги.",
            ];

            if ($usersCount > 0) {
                $lines[] = '';
                $lines[] = '📋 Список переданных друзей:';
                foreach ($users as $index => $sharedUser) {
                    $name = ($sharedUser['first_name'] ?? '') .
                        (isset($sharedUser['last_name']) ? ' ' . $sharedUser['last_name'] : '');
                    $username = isset($sharedUser['username']) ? ' (@' . $sharedUser['username'] . ')' : '';
                    $lines[] = ($index + 1) . ". {$name}{$username}";
                }
            }

            $confirmationText = implode("\n", $lines);

            $service->sendMessage([
                'chat_id' => $chatId,
                'text' => $confirmationText,
            ]);

            Log::info('✅ ОТЛАДКА: Информация о переданных пользователях обработана', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'users_count' => $usersCount,
                'restaurant_id' => $restaurant->id,
                'step' => 'users_shared_processed'
            ]);

            $this->setAppKeyboard($chatId, $service, $restaurant, 'Спасибо! Друзья добавлены. Откройте приложение или поделитесь ещё.');
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка обработки переданных пользователей', [
                'error' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'chat_id' => $chatId,
                'users_shared' => $usersShared,
                'restaurant_id' => $restaurant->id,
                'step' => 'error_in_handle_users_shared'
            ]);

            // Заменяем клавиатуру даже при ошибке
            $this->setAppKeyboard($chatId, $service, $restaurant, 'Спасибо! Друзья добавлены. Откройте приложение или поделитесь ещё.');
        }
    }

    /**
     * Попробовать отправить приветственное сообщение приглашенному пользователю.
     * Если бот не может инициировать чат, отправляет приглашателю ссылку /start для друга.
     */
    private function tryGreetInvitedUser(int $invitedTelegramId, int $inviterChatId, string $inviterName, Restaurant $restaurant, TelegramBotService $service, string $invitedDisplayName = ''): void
    {
        $directSent = false;
        try {
            $lines = [
                'Привет' . ($invitedDisplayName ? ", {$invitedDisplayName}" : '') . "! {$inviterName} пригласил(а) вас в {$restaurant->name}.",
                'Откройте приложение, чтобы посмотреть меню, фото, новости и события,',
                'а также узнать, кто из друзей поставил Repeat.',
            ];

            $service->sendMessage([
                'chat_id' => $invitedTelegramId,
                'text' => implode("\n", $lines),
            ]);

            Log::info('✅ Отправлено приветственное сообщение приглашенному пользователю', [
                'restaurant_id' => $restaurant->id,
                'invited_telegram_id' => $invitedTelegramId,
                'inviter_chat_id' => $inviterChatId,
            ]);
            $directSent = true;
        } catch (Throwable $e) {
            Log::warning('⚠️ Не удалось отправить приветствие приглашенному пользователю (возможно, чат не инициализирован)', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
                'invited_telegram_id' => $invitedTelegramId,
                'inviter_chat_id' => $inviterChatId,
            ]);
        }

        // Пытаемся отправить приглашателю ссылку с deep-link для друга
        $botUsername = null;
        try {
            $botInfo = $service->getMe();
            $botUsername = $botInfo['result']['username'] ?? null;
        } catch (Throwable $e) {
            Log::warning('⚠️ Не удалось получить username бота через getMe', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
            ]);
        }

        // Фолбэк: если API не вернул username, используем сохранённый в ресторане bot_username
        if (!$botUsername && !empty($restaurant->bot_username)) {
            $botUsername = (string) $restaurant->bot_username;
            Log::info('Использован restaurant.bot_username для инвайт-ссылки', [
                'restaurant_id' => $restaurant->id,
                'bot_username' => $botUsername,
            ]);
        }

        $payload = 'r' . $restaurant->id . '-i' . $inviterChatId;
        $startLink = $botUsername ? "https://t.me/{$botUsername}?start={$payload}" : null;

        $fallbackLines = $directSent
            ? [
                'На всякий случай — вот ссылка для друга:',
                $startLink ?? '',
            ]
            : array_filter([
                'Мы не смогли написать приглашенному пользователю. Попросите его начать чат с ботом.',
                $startLink ? "Ссылка: {$startLink}" : null,
            ]);

        try {
            $service->sendMessage([
                'chat_id' => $inviterChatId,
                'text' => implode("\n", $fallbackLines),
            ]);
        } catch (Throwable $e) {
            Log::warning('⚠️ Не удалось отправить fallback-сообщение приглашателю', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
                'inviter_chat_id' => $inviterChatId,
            ]);
        }
    }

    /**
     * Склонение существительных по числам (русский язык)
     */
    private function pluralizeRussian(int $number, string $one, string $few, string $many): string
    {
        $number = abs($number);

        if ($number % 100 >= 11 && $number % 100 <= 19) {
            return $many;
        }

        switch ($number % 10) {
            case 1:
                return $one;
            case 2:
            case 3:
            case 4:
                return $few;
            default:
                return $many;
        }
    }

    /**
     * Найти или создать пользователя-друга по данным из Telegram
     */
    private function findOrCreateFriendUser(int $telegramId, array $sharedUser, ?array $userInfo, ?string $avatarUrl, ?Restaurant $restaurant = null): ?User
    {
        try {
            Log::info('🔍 ОТЛАДКА: Поиск пользователя-друга', [
                'telegram_id' => $telegramId,
                'shared_user' => $sharedUser,
                'step' => 'start_find_friend_user'
            ]);

            // 1) Пытаемся найти по username (самый надёжный признак для Telegram)
            $friendUser = null;
            if (!empty($sharedUser['username'])) {
                $friendUser = User::where('username', (string)$sharedUser['username'])->first();
            }

            // 2) Если username отсутствует — пробуем найти по имени/фамилии (менее надёжно)
            if (!$friendUser && !empty($sharedUser['first_name'])) {
                $query = User::query()->where('first_name', (string)$sharedUser['first_name']);
                if (!empty($sharedUser['last_name'])) {
                    $query->where('last_name', (string)$sharedUser['last_name']);
                }
                $friendUser = $query->first();
            }

            if ($friendUser) {
                Log::info('✅ ОТЛАДКА: Пользователь-друг найден в базе', [
                    'friend_user_id' => $friendUser->id,
                    'telegram_id' => $telegramId,
                    'step' => 'existing_friend_user_found'
                ]);

                // Обновляем информацию, если есть новые данные
                $updateData = [];

                if (!empty($sharedUser['first_name']) && $sharedUser['first_name'] !== $friendUser->first_name) {
                    $updateData['first_name'] = $sharedUser['first_name'];
                }

                if (!empty($sharedUser['last_name']) && $sharedUser['last_name'] !== $friendUser->last_name) {
                    $updateData['last_name'] = $sharedUser['last_name'];
                }

                if (!empty($sharedUser['username']) && $sharedUser['username'] !== $friendUser->username) {
                    $updateData['username'] = $sharedUser['username'];
                }

                // Если удалось получить фото из sharedUser->photo — вместо CDN URL сохраняем file_id
                if (!empty($sharedUser['photo']) && is_array($sharedUser['photo'])) {
                    $photos = $sharedUser['photo'];
                    $highest = end($photos);
                    if (is_array($highest) && !empty($highest['file_id'])) {
                        $fileId = (string) $highest['file_id'];
                        if ($fileId !== $friendUser->avatar_url) {
                            $updateData['avatar_url'] = $fileId;
                        }
                    }
                } elseif (!empty($avatarUrl) && $avatarUrl !== $friendUser->avatar_url) {
                    // Фолбэк: если нет photo массива, сохраняем как раньше (CDN URL)
                    $updateData['avatar_url'] = $avatarUrl;
                }

                if (!empty($updateData)) {
                    $friendUser->update($updateData);
                    Log::info('🔄 ОТЛАДКА: Обновлена информация о пользователе-друге', [
                        'friend_user_id' => $friendUser->id,
                        'updated_data' => $updateData,
                        'step' => 'friend_user_updated'
                    ]);
                }

                return $friendUser;
            }

            // Создаем нового пользователя, если не найден
            Log::info('➕ ОТЛАДКА: Создание нового пользователя-друга', [
                'telegram_id' => $telegramId,
                'shared_user' => $sharedUser,
                'step' => 'creating_new_friend_user'
            ]);

            $userData = [
                'first_name' => $sharedUser['first_name'] ?? 'Неизвестно',
                'last_name' => $sharedUser['last_name'] ?? null,
                'username' => $sharedUser['username'] ?? null,
                // Если есть photo — сохраняем file_id вместо CDN URL
                'avatar_url' => (function () use ($sharedUser, $avatarUrl) {
                    if (isset($sharedUser['photo']) && is_array($sharedUser['photo'])) {
                        $photos = $sharedUser['photo'];
                        $highest = end($photos);
                        if (is_array($highest) && !empty($highest['file_id'])) {
                            return (string) $highest['file_id'];
                        }
                    }
                    return $avatarUrl;
                })(),
            ];

            $friendUser = User::create($userData);

            Log::info('✅ ОТЛАДКА: Новый пользователь-друг создан', [
                'friend_user_id' => $friendUser->id,
                'telegram_id' => $telegramId,
                'user_data' => $userData,
                'step' => 'new_friend_user_created'
            ]);

            return $friendUser;
        } catch (Throwable $e) {
            Log::error('❌ ОТЛАДКА: Ошибка поиска/создания пользователя-друга', [
                'error' => $e->getMessage(),
                'telegram_id' => $telegramId,
                'shared_user' => $sharedUser,
                'step' => 'error_find_create_friend_user'
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

        // Показываем обновленную клавиатуру (с контактами и приложением)  
        $this->setAppKeyboard($chatId, $service, $restaurant, 'Откройте приложение или поделитесь контактами и друзьями.');

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

            $user = User::whereHas('restaurants', function ($q) use ($restaurant, $chatId) {
                $q->where('restaurant_id', $restaurant->id)
                    ->where('chat_id', (string)$chatId);
            })->first();

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

            // Получаем дополнительную информацию о пользователе (аватар)
            $avatarUrl = null;
            if ($userId) {
                $avatarUrl = $this->getUserAvatarUrl((int)$userId, $service);
            }

            // Обновляем данные пользователя
            $updateData = [
                'phone' => $phoneNumber,
            ];

            // Обновляем имя из контакта, если оно отличается
            if (!empty($firstName) && $firstName !== $user->first_name) {
                $updateData['first_name'] = $firstName;
                Log::info('📞 ОТЛАДКА: Обновляем имя из контакта', [
                    'old_first_name' => $user->first_name,
                    'new_first_name' => $firstName,
                    'step' => 'updating_first_name_from_contact'
                ]);
            }

            if (!empty($lastName) && $lastName !== $user->last_name) {
                $updateData['last_name'] = $lastName;
                Log::info('📞 ОТЛАДКА: Обновляем фамилию из контакта', [
                    'old_last_name' => $user->last_name,
                    'new_last_name' => $lastName,
                    'step' => 'updating_last_name_from_contact'
                ]);
            }

            // Обновляем аватар, если получили новый
            if ($avatarUrl && $avatarUrl !== $user->avatar_url) {
                $updateData['avatar_url'] = $avatarUrl;
                Log::info('📞 ОТЛАДКА: Обновляем аватар пользователя', [
                    'old_avatar_url' => $user->avatar_url,
                    'new_avatar_url' => $avatarUrl,
                    'step' => 'updating_avatar_from_contact'
                ]);
            }

            $user->update($updateData);

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

            $lines = [
                '✅ Спасибо! Контакт сохранен.',
            ];
            if (!empty($messageText)) {
                $lines[] = '';
                $lines[] = $messageText;
            }
            $text = implode("\n", $lines);

            $service->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
            ]);

            Log::info('📞 ОТЛАДКА: Подтверждение отправлено', [
                'chat_id' => $chatId,
                'step' => 'confirmation_sent'
            ]);

            // Устанавливаем постоянную клавиатуру с приложением и контактами
            Log::info('⌨️ ОТЛАДКА: Установка постоянной клавиатуры', [
                'chat_id' => $chatId,
                'step' => 'setting_persistent_keyboard'
            ]);

            $this->setAppKeyboard($chatId, $service, $restaurant);

            Log::info('✅ ОТЛАДКА: Постоянная клавиатура установлена', [
                'chat_id' => $chatId,
                'step' => 'persistent_keyboard_set'
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
            $this->setAppKeyboard($chatId, $service, $restaurant, 'Откройте приложение или поделитесь контактами и друзьями.');
        }
    }





    /**
     * Установить постоянную клавиатуру с приложением и контактами
     */
    private function setAppKeyboard(int $chatId, TelegramBotService $service, Restaurant $restaurant, ?string $messageText = null): void
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

            // Создаем постоянную клавиатуру БЕЗ кнопки открытия приложения
            // и с кнопкой поделиться контактом только если телефона ещё нет

            $buttons = [];

            $user = User::whereHas('restaurants', function ($q) use ($restaurant, $chatId) {
                $q->where('restaurant_id', $restaurant->id)
                    ->where('chat_id', (string)$chatId);
            })->first();

            if (!$user || empty($user->phone)) {
                $buttons[] = [
                    [
                        'text' => '📞 Поделиться контактом',
                        'request_contact' => true,
                    ],
                    [
                        'text' => '👥 Поделиться друзьями',
                        'request_users' => [
                            'request_id' => 1,
                            'user_is_bot' => false,
                            'max_quantity' => 10,
                            'request_name' => true,
                            'request_username' => true,
                            'request_photo' => true,
                        ],
                    ],
                ];
            } else {
                $buttons[] = [
                    [
                        'text' => '👥 Поделиться друзьями',
                        'request_users' => [
                            'request_id' => 1,
                            'user_is_bot' => false,
                            'max_quantity' => 10,
                            'request_name' => true,
                            'request_username' => true,
                            'request_photo' => true,
                        ],
                    ],
                ];
            }

            // Всегда добавляем кнопку для создания пригласительной ссылки
            $buttons[] = [
                [
                    'text' => 'Пригласить друга',
                ],
            ];

            $replyMarkup = $service->createReplyKeyboard($buttons, true, false);

            Log::info('⌨️ ОТЛАДКА: Клавиатура создана', [
                'reply_markup' => $replyMarkup,
                'step' => 'keyboard_created'
            ]);

            Log::info('⌨️ ОТЛАДКА: Отправка сообщения с новой клавиатурой', [
                'chat_id' => $chatId,
                'step' => 'sending_keyboard_message'
            ]);

            $lines = [
                '✅ Отлично, всё получилось!',
                '',
                'Осталось только добавить друзей. Они смогут видеть ваши отметки и делиться своими.',
            ];
            if ($messageText !== null) {
                $lines = [$messageText];
            }
            $text = implode("\n", $lines);

            $result = $service->sendMessage([
                'chat_id' => $chatId,
                'text' => $text,
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
     * Сформировать и отправить пользователю персональную ссылку-приглашение
     */
    private function handleCreateInviteLink(int $chatId, TelegramBotService $service, Restaurant $restaurant): void
    {
        try {
            // Получаем username бота
            $botUsername = null;
            try {
                $botInfo = $service->getMe();
                $botUsername = $botInfo['result']['username'] ?? null;
            } catch (Throwable $e) {
                Log::warning('Не удалось получить username бота через getMe', [
                    'error' => $e->getMessage(),
                    'restaurant_id' => $restaurant->id,
                ]);
            }

            if (!$botUsername && !empty($restaurant->bot_username)) {
                $botUsername = (string) $restaurant->bot_username;
            }

            if (!$botUsername) {
                $service->sendMessage([
                    'chat_id' => $chatId,
                    'text' => '❌ Не удалось сформировать ссылку. Попробуйте позже.',
                ]);
                return;
            }

            $payload = 'r' . $restaurant->id . '-i' . $chatId;
            $inviteLink = 'https://t.me/' . $botUsername . '?start=' . $payload;

            $lines = [
                '🔗 Ваша персональная ссылка-приглашение готова. Отправьте ее другу.',
                $inviteLink,
                '',
                'Когда он присоединится, вы начнете видеть отметки "Repeat" друг друга во всех ресторанах, подключенных к платформе Eat.Drink.Repeat.',
            ];

            $service->sendMessage([
                'chat_id' => $chatId,
                'text' => implode("\n", $lines),
            ]);

            Log::info('Сформирована ссылка-приглашение', [
                'restaurant_id' => $restaurant->id,
                'chat_id' => $chatId,
                'invite_link' => $inviteLink,
            ]);
        } catch (Throwable $e) {
            Log::error('Ошибка создания ссылки-приглашения', [
                'error' => $e->getMessage(),
                'restaurant_id' => $restaurant->id,
                'chat_id' => $chatId,
            ]);

            $service->sendMessage([
                'chat_id' => $chatId,
                'text' => '❌ Произошла ошибка при создании ссылки. Попробуйте позже.',
            ]);
        }
    }

    /**
     * Найти пользователя по chat_id
     */
    private function findByChatId(string $chatId): ?User
    {
        return User::whereHas('restaurants', function ($q) use ($chatId) {
            $q->where('chat_id', $chatId);
        })->first();
    }

    /**
     * Проверить есть ли пользователь с данным chat_id
     */
    private function existsByChatId(string $chatId): bool
    {
        return User::whereHas('restaurants', function ($q) use ($chatId) {
            $q->where('chat_id', $chatId);
        })->exists();
    }
}
