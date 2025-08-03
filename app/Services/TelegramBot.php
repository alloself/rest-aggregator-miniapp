<?php

namespace App\Services;

use App\Models\Restaurant;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Режимы парсинга сообщений Telegram
 */
enum ParseMode: string
{
    case HTML = 'HTML';
    case MARKDOWN = 'Markdown';
    case MARKDOWN_V2 = 'MarkdownV2';
    case NONE = '';
}

/**
 * Telegram Bot API Service
 * 
 * Современный сервис для работы с Telegram Bot API 9.1
 * Поддерживает мультибот архитектуру для ресторанов
 * 
 * @author Your Name
 * @version 1.0
 */
class TelegramBot
{
    private Client $httpClient;
    private string $baseUrl = 'https://api.telegram.org/bot';
    private ?string $botToken = null;
    private ?Restaurant $restaurant = null;

    public function __construct(?string $botToken = null)
    {
        $this->httpClient = new Client([
            'timeout' => 30,
            'verify' => true, // Включаем проверку SSL сертификатов для безопасности
            'http_errors' => false,
        ]);

        if ($botToken) {
            $this->setBotToken($botToken);
        }
    }

    /**
     * Создать инстанс для конкретного ресторана
     */
    public static function forRestaurant(Restaurant $restaurant): self
    {
        if (empty($restaurant->telegram_bot_token)) {
            throw new Exception("Токен Telegram бота не установлен для ресторана {$restaurant->name}");
        }

        $instance = new self($restaurant->telegram_bot_token);
        $instance->restaurant = $restaurant;
        
        return $instance;
    }

    /**
     * Установить токен бота
     */
    public function setBotToken(string $token): self
    {
        // Валидация формата токена согласно документации Telegram
        // Формат: 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
        if (!preg_match('/^\d+:[A-Za-z0-9_-]+$/', $token)) {
            throw new Exception('Недопустимый формат токена Telegram бота');
        }
        
        // Проверяем, что токен содержит двоеточие и обе части не пустые
        $parts = explode(':', $token);
        if (count($parts) !== 2 || empty($parts[0]) || empty($parts[1])) {
            throw new Exception('Токен бота должен содержать ID и секретную часть, разделенные двоеточием');
        }
        
        $this->botToken = $token;
        return $this;
    }

    /**
     * Выполнить запрос к Telegram Bot API
     */
    private function makeRequest(string $method, array $params = []): array
    {
        if (!$this->botToken) {
            throw new Exception('Токен бота не установлен');
        }

        $url = $this->baseUrl . $this->botToken . '/' . $method;

        try {
            $options = [];
            
            // Если есть файлы, используем multipart/form-data
            if ($this->hasFiles($params)) {
                $options['multipart'] = $this->buildMultipart($params);
            } else {
                $options['json'] = $params;
            }

            $response = $this->httpClient->post($url, $options);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if ($data['ok'] ?? false) {
                return $data['result'] ?? [];
            }

            $errorMessage = $data['description'] ?? 'Неизвестная ошибка';
            Log::error('Telegram API Error', [
                'method' => $method,
                'error' => $errorMessage,
                'error_code' => $data['error_code'] ?? null,
                'restaurant_id' => $this->restaurant?->id,
            ]);

            throw new Exception("Telegram API Error: {$errorMessage}");

        } catch (GuzzleException $e) {
            Log::error('Telegram HTTP Error', [
                'method' => $method,
                'error' => $e->getMessage(),
                'restaurant_id' => $this->restaurant?->id,
            ]);
            
            throw new Exception("HTTP Error: {$e->getMessage()}");
        }
    }

    // =================
    // ОСНОВНЫЕ МЕТОДЫ
    // =================

    /**
     * Получить информацию о боте
     */
    public function getMe(): array
    {
        return $this->makeRequest('getMe');
    }

    /**
     * Отправить текстовое сообщение
     * 
     * @param int|string $chatId ID чата или username канала
     * @param string $text Текст сообщения (до 4096 символов)
     * @param ParseMode $parseMode Режим парсинга сообщения
     * @param array<string, mixed>|null $keyboard Inline-клавиатура
     * @param int|null $replyToMessageId ID сообщения для ответа
     * @param bool $disableNotification Отключить уведомления
     * @return array<string, mixed> Результат API запроса
     * @throws Exception При ошибке валидации или отправки
     */
    public function sendMessage(
        int|string $chatId,
        string $text,
        ParseMode $parseMode = ParseMode::HTML,
        ?array $keyboard = null,
        ?int $replyToMessageId = null,
        bool $disableNotification = false
    ): array {
        // Валидация входных данных
        if (empty($text)) {
            throw new Exception('Текст сообщения не может быть пустым');
        }
        
        if (mb_strlen($text) > 4096) {
            throw new Exception('Текст сообщения не может превышать 4096 символов');
        }

        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode->value,
            'disable_notification' => $disableNotification,
        ];

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        if ($replyToMessageId) {
            $params['reply_parameters'] = ['message_id' => $replyToMessageId];
        }

        return $this->makeRequest('sendMessage', $params);
    }

    /**
     * Отправить фото
     * 
     * @param int|string $chatId ID чата или username канала
     * @param string $photo File ID, URL или путь к файлу
     * @param string|null $caption Подпись к фото (до 1024 символов)
     * @param ParseMode $parseMode Режим парсинга подписи
     * @param array<string, mixed>|null $keyboard Inline-клавиатура
     * @return array<string, mixed> Результат API запроса
     * @throws Exception При ошибке отправки
     */
    public function sendPhoto(
        int|string $chatId,
        string $photo,
        ?string $caption = null,
        ParseMode $parseMode = ParseMode::HTML,
        ?array $keyboard = null
    ): array {
        $params = [
            'chat_id' => $chatId,
            'photo' => $photo,
        ];

        if ($caption) {
            // Валидация длины подписи
            if (mb_strlen($caption) > 1024) {
                throw new Exception('Подпись к фото не может превышать 1024 символа');
            }
            
            $params['caption'] = $caption;
            $params['parse_mode'] = $parseMode->value;
        }

        if ($keyboard) {
            $params['reply_markup'] = json_encode($keyboard);
        }

        return $this->makeRequest('sendPhoto', $params);
    }

    /**
     * Отправить местоположение
     */
    public function sendLocation(
        int|string $chatId,
        float $latitude,
        float $longitude,
        ?string $title = null,
        ?string $address = null
    ): array {
        $params = [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        return $this->makeRequest('sendLocation', $params);
    }

    /**
     * Отправить контакт
     */
    public function sendContact(
        int|string $chatId,
        string $phoneNumber,
        string $firstName,
        ?string $lastName = null
    ): array {
        $params = [
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'first_name' => $firstName,
        ];

        if ($lastName) {
            $params['last_name'] = $lastName;
        }

        return $this->makeRequest('sendContact', $params);
    }

    // ===================
    // НАСТРОЙКА БОТА
    // ===================

    /**
     * Установить webhook
     */
    public function setWebhook(string $url, array $allowedUpdates = []): array
    {
        $params = ['url' => $url];
        
        if (!empty($allowedUpdates)) {
            $params['allowed_updates'] = $allowedUpdates;
        }

        return $this->makeRequest('setWebhook', $params);
    }

    /**
     * Удалить webhook
     */
    public function deleteWebhook(bool $dropPendingUpdates = false): array
    {
        return $this->makeRequest('deleteWebhook', [
            'drop_pending_updates' => $dropPendingUpdates
        ]);
    }

    /**
     * Получить информацию о webhook
     */
    public function getWebhookInfo(): array
    {
        return $this->makeRequest('getWebhookInfo');
    }

    /**
     * Установить команды бота
     */
    public function setMyCommands(array $commands): array
    {
        $formattedCommands = [];
        foreach ($commands as $command => $description) {
            $formattedCommands[] = [
                'command' => $command,
                'description' => $description
            ];
        }

        return $this->makeRequest('setMyCommands', [
            'commands' => $formattedCommands
        ]);
    }

    /**
     * Установить название бота
     */
    public function setMyName(string $name): array
    {
        return $this->makeRequest('setMyName', ['name' => $name]);
    }

    /**
     * Установить описание бота
     */
    public function setMyDescription(string $description): array
    {
        return $this->makeRequest('setMyDescription', ['description' => $description]);
    }

    // ======================
    // СПЕЦИАЛЬНЫЕ МЕТОДЫ ДЛЯ РЕСТОРАНОВ
    // ======================

    /**
     * Отправить приветственное сообщение ресторана
     */
    public function sendRestaurantWelcome(int|string $chatId): array
    {
        if (!$this->restaurant) {
            throw new Exception('Ресторан не установлен');
        }

        $text = "🍽 <b>Добро пожаловать в {$this->restaurant->name}!</b>\n\n";
        
        if ($this->restaurant->subtitle) {
            $text .= "{$this->restaurant->subtitle}\n\n";
        }

        $text .= "Выберите действие:";

        $keyboard = $this->getMainMenuKeyboard();

        return $this->sendMessage($chatId, $text, ParseMode::HTML, $keyboard);
    }

    /**
     * Отправить информацию о ресторане
     */
    public function sendRestaurantInfo(int|string $chatId): array
    {
        if (!$this->restaurant) {
            throw new Exception('Ресторан не установлен');
        }

        $text = "ℹ️ <b>Информация о ресторане</b>\n\n";
        $text .= "📍 <b>Название:</b> {$this->restaurant->name}\n";
        
        if ($this->restaurant->address) {
            $text .= "🏠 <b>Адрес:</b> {$this->restaurant->address}\n";
        }
        
        if ($this->restaurant->phone) {
            $text .= "📞 <b>Телефон:</b> {$this->restaurant->phone}\n";
        }

        if ($this->restaurant->working_hours) {
            $text .= "🕒 <b>Часы работы:</b>\n";
            foreach ($this->restaurant->working_hours as $day => $hours) {
                $text .= "• {$day}: {$hours}\n";
            }
        }

        if ($this->restaurant->average_receipt) {
            $text .= "💰 <b>Средний чек:</b> {$this->restaurant->average_receipt}\n";
        }

        return $this->sendMessage($chatId, $text, ParseMode::HTML);
    }

    /**
     * Отправить контакты ресторана
     */
    public function sendRestaurantContact(int|string $chatId): array
    {
        if (!$this->restaurant || !$this->restaurant->phone) {
            throw new Exception('Контактная информация ресторана не установлена');
        }

        return $this->sendContact(
            $chatId,
            $this->restaurant->phone,
            $this->restaurant->name
        );
    }

    // ===================
    // НОВЫЕ ВОЗМОЖНОСТИ BOT API 9.1
    // ===================

    /**
     * Отправить чек-лист (Bot API 9.1)
     * 
     * Отправляет чек-лист от имени бизнес-аккаунта.
     * Требует подключения бизнес-аккаунта к боту.
     * 
     * @param int|string $chatId ID чата
     * @param array $tasks Массив задач чек-листа
     * @param string|null $title Заголовок чек-листа
     * @return array Результат запроса API
     * @throws Exception При ошибке отправки
     */
    public function sendChecklist(int|string $chatId, array $tasks, ?string $title = null): array
    {
        // Валидация входных данных
        if (empty($tasks)) {
            throw new Exception('Список задач не может быть пустым');
        }

        $checklistTasks = [];
        foreach ($tasks as $task) {
            if (is_string($task)) {
                $checklistTasks[] = [
                    'text' => $task,
                    'is_done' => false
                ];
            } elseif (is_array($task) && isset($task['text'])) {
                $checklistTasks[] = [
                    'text' => $task['text'],
                    'is_done' => $task['is_done'] ?? false
                ];
            } else {
                throw new Exception('Неправильный формат задачи в чек-листе');
            }
        }

        $params = [
            'chat_id' => $chatId,
            'checklist' => [
                'tasks' => $checklistTasks
            ]
        ];

        if ($title) {
            $params['checklist']['title'] = $title;
        }

        return $this->makeRequest('sendChecklist', $params);
    }

    /**
     * Получить баланс Telegram Stars (Bot API 9.1)
     * 
     * Возвращает текущий баланс Telegram Stars бота.
     * Позволяет отслеживать доходы от платных функций.
     * 
     * @return array Информация о балансе Stars
     * @throws Exception При ошибке получения баланса
     */
    public function getMyStarBalance(): array
    {
        return $this->makeRequest('getMyStarBalance');
    }

    /**
     * Редактировать чек-лист в сообщении (Bot API 9.1)
     * 
     * @param int|string $chatId ID чата
     * @param int $messageId ID сообщения
     * @param array $tasks Новый список задач
     * @param string|null $title Новый заголовок
     * @return array Результат запроса
     */
    public function editMessageChecklist(
        int|string $chatId, 
        int $messageId, 
        array $tasks, 
        ?string $title = null
    ): array {
        if (empty($tasks)) {
            throw new Exception('Список задач не может быть пустым');
        }

        $checklistTasks = [];
        foreach ($tasks as $task) {
            if (is_string($task)) {
                $checklistTasks[] = [
                    'text' => $task,
                    'is_done' => false
                ];
            } elseif (is_array($task) && isset($task['text'])) {
                $checklistTasks[] = [
                    'text' => $task['text'],
                    'is_done' => $task['is_done'] ?? false
                ];
            } else {
                throw new Exception('Неправильный формат задачи в чек-листе');
            }
        }

        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'checklist' => [
                'tasks' => $checklistTasks
            ]
        ];

        if ($title) {
            $params['checklist']['title'] = $title;
        }

        return $this->makeRequest('editMessageChecklist', $params);
    }

    // ===================
    // ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ
    // ===================

    /**
     * Получить основную клавиатуру меню
     */
    private function getMainMenuKeyboard(): array
    {
        return [
            'inline_keyboard' => [
                [
                    ['text' => '📋 Меню', 'callback_data' => 'menu'],
                    ['text' => 'ℹ️ О ресторане', 'callback_data' => 'info']
                ],
                [
                    ['text' => '📞 Контакты', 'callback_data' => 'contact'],
                    ['text' => '📍 Местоположение', 'callback_data' => 'location']
                ],
                [
                    ['text' => '🕒 Часы работы', 'callback_data' => 'hours'],
                    ['text' => '🛒 Заказать', 'callback_data' => 'order']
                ]
            ]
        ];
    }

    /**
     * Проверить есть ли файлы в параметрах
     */
    private function hasFiles(array $params): bool
    {
        foreach ($params as $value) {
            if (is_string($value) && (str_starts_with($value, '@') || file_exists($value))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Построить multipart данные для загрузки файлов
     */
    private function buildMultipart(array $params): array
    {
        $multipart = [];
        
        foreach ($params as $name => $value) {
            if (is_string($value) && file_exists($value)) {
                $resource = fopen($value, 'r');
                if ($resource === false) {
                    throw new Exception("Не удалось открыть файл: {$value}");
                }
                
                $multipart[] = [
                    'name' => $name,
                    'contents' => $resource,
                    'filename' => basename($value)
                ];
            } else {
                $multipart[] = [
                    'name' => $name,
                    'contents' => is_array($value) ? json_encode($value) : (string)$value
                ];
            }
        }
        
        return $multipart;
    }

    /**
     * Получить инстанс для всех ресторанов (статический метод)
     */
    public static function setupAllRestaurants(): array
    {
        $results = [];
        $restaurants = Restaurant::whereNotNull('telegram_bot_token')->get();

        foreach ($restaurants as $restaurant) {
            try {
                $bot = self::forRestaurant($restaurant);
                $results[$restaurant->id] = $bot->setupRestaurantBot();
            } catch (Exception $e) {
                $results[$restaurant->id] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Настроить бота для ресторана
     */
    public function setupRestaurantBot(): array
    {
        if (!$this->restaurant) {
            throw new Exception('Ресторан не установлен');
        }

        $results = [];

        // Установить название
        try {
            $results['name'] = $this->setMyName($this->restaurant->name);
        } catch (Exception $e) {
            $results['name'] = ['error' => $e->getMessage()];
        }

        // Установить описание
        if ($this->restaurant->subtitle) {
            try {
                $results['description'] = $this->setMyDescription($this->restaurant->subtitle);
            } catch (Exception $e) {
                $results['description'] = ['error' => $e->getMessage()];
            }
        }

        // Установить команды
        try {
            $commands = [
                'start' => 'Начать работу с ботом',
                'menu' => 'Посмотреть меню',
                'info' => 'Информация о ресторане',
                'contact' => 'Контактная информация',
                'location' => 'Местоположение ресторана',
                'hours' => 'Часы работы',
                'order' => 'Сделать заказ'
            ];
            $results['commands'] = $this->setMyCommands($commands);
        } catch (Exception $e) {
            $results['commands'] = ['error' => $e->getMessage()];
        }

        return $results;
    }
}
