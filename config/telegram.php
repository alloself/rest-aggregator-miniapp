<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Configuration
    |--------------------------------------------------------------------------
    |
    | Конфигурация для Telegram бота
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | Bot Settings
    |--------------------------------------------------------------------------
    */
    
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),
    
    'bot_name' => env('TELEGRAM_BOT_NAME', 'Hotel Booking Bot'),
    
    /*
    |--------------------------------------------------------------------------
    | Webhook Settings
    |--------------------------------------------------------------------------
    */
    
    'webhook_certificate' => env('TELEGRAM_WEBHOOK_CERTIFICATE'),
    
    'webhook_ip_address' => env('TELEGRAM_WEBHOOK_IP_ADDRESS'),
    
    'webhook_max_connections' => env('TELEGRAM_WEBHOOK_MAX_CONNECTIONS', 40),
    
    'webhook_allowed_updates' => [
        'message',
        'callback_query',
        'inline_query',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    
    'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
    
    'timeout' => env('TELEGRAM_TIMEOUT', 30),
    
    'connect_timeout' => env('TELEGRAM_CONNECT_TIMEOUT', 10),
    
    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    
    'log_updates' => env('TELEGRAM_LOG_UPDATES', true),
    
    'log_channel' => env('TELEGRAM_LOG_CHANNEL', 'telegram'),
    
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    
    'rate_limit' => [
        'enabled' => env('TELEGRAM_RATE_LIMIT_ENABLED', true),
        'max_requests' => env('TELEGRAM_RATE_LIMIT_MAX_REQUESTS', 30),
        'per_minutes' => env('TELEGRAM_RATE_LIMIT_PER_MINUTES', 1),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    */
    
    'commands' => [
        'start' => [
            'description' => 'Начать работу с ботом',
            'scope' => 'default',
        ],
        'help' => [
            'description' => 'Получить справку',
            'scope' => 'default',
        ],
        'search' => [
            'description' => 'Поиск отелей',
            'scope' => 'default',
        ],
        'mybookings' => [
            'description' => 'Мои бронирования',
            'scope' => 'default',
        ],
        'cancel' => [
            'description' => 'Отменить текущую операцию',
            'scope' => 'default',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */
    
    'messages' => [
        'welcome' => '🏨 Добро пожаловать в систему бронирования отелей!',
        'error' => '❌ Произошла ошибка. Попробуйте позже.',
        'maintenance' => '🔧 Бот находится на техническом обслуживании.',
        'rate_limit' => '⏰ Слишком много запросов. Попробуйте позже.',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    */
    
    'features' => [
        'inline_mode' => env('TELEGRAM_INLINE_MODE', false),
        'payments' => env('TELEGRAM_PAYMENTS', false),
        'web_app' => env('TELEGRAM_WEB_APP', false),
        'mini_app' => env('TELEGRAM_MINI_APP', false),
    ],
]; 