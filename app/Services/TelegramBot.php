<?php

namespace App\Services;

use App\Models\Restaurant;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use RuntimeException;
use Illuminate\Support\Facades\Storage;

/**
 * Telegram Bot/WebApp helper for restaurant-scoped interactions.
 *
 * References:
 * - Telegram Mini Apps: https://core.telegram.org/bots/webapps
 * - Telegram Bot API: https://core.telegram.org/bots/api
 */
class TelegramBot
{
    private readonly string $token;
    private readonly Client $httpClient;
    private readonly bool $useTestEnvironment;
    private ?Restaurant $restaurantContext = null;

    /**
     * Create a service for an arbitrary bot token.
     */
    public static function forToken(string $botToken, bool $useTestEnvironment = false): self
    {
        return new self($botToken, $useTestEnvironment);
    }

    /**
     * Create a service bound to a specific restaurant (uses `telegram_bot_token`).
     */
    public static function forRestaurant(Restaurant $restaurant, ?bool $useTestEnvironment = null): self
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            throw new RuntimeException('Restaurant has no telegram_bot_token configured.');
        }

        $service = new self(
            $token,
            $useTestEnvironment ?? (bool) config('services.telegram.test_environment', false)
        );
        $service->restaurantContext = $restaurant;

        return $service;
    }

    private function __construct(string $token, bool $useTestEnvironment)
    {
        $this->token = $token;
        $this->useTestEnvironment = $useTestEnvironment;
        $this->httpClient = new Client([
            'base_uri' => 'https://api.telegram.org/',
            'http_errors' => false,
            'timeout' => 10,
        ]);
    }

    /**
     * Perform a Bot API call.
     *
     * @param array<string,mixed> $params
     * @return mixed Result field from Telegram response (array|bool|string|int|null)
     */
    public function api(string $method, array $params = []): mixed
    {
        $segments = ['/bot'.$this->token.'/'];
        if ($this->useTestEnvironment) {
            $segments[] = 'test/';
        }
        $segments[] = ltrim($method, '/');
        $path = implode('', $segments);

        try {
            $response = $this->httpClient->post($path, [
                'json' => $params,
            ]);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Telegram API request failed: '.$e->getMessage(), previous: $e);
        }

        $payload = json_decode((string) $response->getBody(), true) ?: [];

        if (!is_array($payload) || ($payload['ok'] ?? false) !== true) {
            $description = (string) Arr::get($payload, 'description', 'unknown error');
            $errorCode = (int) Arr::get($payload, 'error_code', 0);
            throw new RuntimeException("Telegram API error ({$errorCode}): {$description}");
        }

        return $payload['result'] ?? null;
    }

    // ============ General Bot API helpers ============

    /**
     * Get bot info (getMe).
     * @return array<string,mixed>
     */
    public function getMe(): array
    {
        return $this->api('getMe');
    }

    /**
     * Set the bot's name (first name) visible in contacts and elsewhere.
     * Uses setMyName (Bot API 6.7+). Optionally pass a language_code for localized names.
     *
     * @param array{language_code?:string} $options
     * @return bool
     */
    public function setMyName(string $firstName, array $options = []): bool
    {
        $params = array_merge(['name' => $firstName], $options);
        return (bool) $this->api('setMyName', $params);
    }

    /**
     * Set the bot's description shown in the bot profile.
     * Telegram allows up to 512 characters; we conservatively truncate.
     *
     * @param array{language_code?:string} $options
     */
    public function setMyDescription(string $description, array $options = []): bool
    {
        $normalized = $this->normalizeTextForTelegram($description, 512);
        $params = array_merge(['description' => $normalized], $options);
        return (bool) $this->api('setMyDescription', $params);
    }

    /**
     * Set the bot's short description (shown on the bot profile in the chat list).
     * Telegram allows up to 120 characters; truncate accordingly.
     *
     * @param array{language_code?:string} $options
     */
    public function setMyShortDescription(string $shortDescription, array $options = []): bool
    {
        $normalized = $this->normalizeTextForTelegram($shortDescription, 120);
        $params = array_merge(['short_description' => $normalized], $options);
        return (bool) $this->api('setMyShortDescription', $params);
    }

    /**
     * Normalize arbitrary HTML/plain text to plain UTF-8 text suitable for Telegram fields.
     */
    private function normalizeTextForTelegram(string $text, int $maxLength): string
    {
        // Decode HTML entities (including &nbsp;) then strip tags
        $decoded = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = strip_tags($decoded);
        // Replace NBSP (U+00A0) with regular space
        $plain = str_replace("\xC2\xA0", ' ', $plain);
        // Collapse whitespace
        $plain = (string) preg_replace('/\s+/u', ' ', $plain);
        $plain = trim($plain);
        // Enforce max length
        if (mb_strlen($plain) > $maxLength) {
            $plain = mb_substr($plain, 0, $maxLength);
        }
        return $plain;
    }

    // ============ Mini App setup helpers ============

    /**
     * Build absolute WebApp URL for a given restaurant (app.url/restaurant/{slug}).
     */
    public function buildRestaurantWebAppUrl(Restaurant $restaurant): string
    {
        $base = rtrim((string) config('app.url'), '/');
        return $base.'/restaurant/'.ltrim((string) $restaurant->slug, '/');
    }

    /**
     * Perform basic setup for a restaurant:
     * - Set webhook to APP_URL/api/telegram/webhook/{restaurant_id}
     * - Reset menu button → default (to refresh cached text)
     * - Set menu button to WebApp (app.url/restaurant/{slug}) with text (default: "Приложение")
     */
    public function setupRestaurantMiniApp(Restaurant $restaurant, string $menuText = 'Приложение'): bool
    {
        // 1) Set webhook
        $webhookUrl = $this->buildRestaurantWebhookUrl($restaurant);
        $this->assertWebhookUrlAllowed($webhookUrl);
        $hookOk = $this->setWebhook($webhookUrl);

        // 2) Configure Mini App menu button
        $url = $this->buildRestaurantWebAppUrl($restaurant);
        $this->assertWebAppUrlAllowed($url);
        // Some Telegram clients cache menu button aggressively. To force-refresh text,
        // first reset to default, then set the WebApp with the desired text and URL.
        $resetOk = (bool) $this->api('setChatMenuButton', [
            'menu_button' => [
                'type' => 'default',
            ],
        ]);

        $setOk = $this->setMenuButtonWebApp($menuText, $url);
        return $hookOk && $resetOk && $setOk;
    }

    /**
     * Validate WebApp URL scheme: HTTPS required in production; HTTP allowed only in test env.
     */
    private function assertWebAppUrlAllowed(string $url): void
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme === 'https') {
            return;
        }
        if ($this->useTestEnvironment && $scheme === 'http') {
            return;
        }
        throw new RuntimeException('WebApp URL must be HTTPS (HTTP allowed only in Telegram test environment).');
    }

    /**
     * Build webhook URL: APP_URL/api/telegram/webhook/{restaurant_id}
     */
    private function buildRestaurantWebhookUrl(Restaurant $restaurant): string
    {
        $base = rtrim((string) config('app.url'), '/');
        return $base.'/api/telegram/webhook/'.$restaurant->getKey();
    }

    /**
     * Telegram requires a valid HTTPS webhook URL with a trusted certificate.
     * Enforce HTTPS regardless of test environment.
     */
    private function assertWebhookUrlAllowed(string $url): void
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme !== 'https') {
            throw new RuntimeException('Webhook URL must be HTTPS with a valid certificate. See SSL_GUIDE.md');
        }
    }

    // ============ Chat photo helpers ============

    /**
     * Update a chat's photo using restaurant image with pivot key 'telegram_avatar'.
     * Works for supergroups/channels where the bot is admin with appropriate rights.
     *
     * @param int|string $chatId
     */
    public function setChatPhotoFromRestaurantAvatar(int|string $chatId, Restaurant $restaurant): bool
    {
        $file = $restaurant->images()->wherePivot('key', 'telegram_avatar')->first();
        if (!$file) {
            return false;
        }

        $storedRelativePath = $file->getRawOriginal('url');
        if (!$storedRelativePath) {
            return false;
        }

        $absolutePath = Storage::disk('public')->path($storedRelativePath);
        if (!is_readable($absolutePath)) {
            return false;
        }

        $multipart = [
            [
                'name' => 'chat_id',
                'contents' => (string) $chatId,
            ],
            [
                'name' => 'photo',
                'contents' => fopen($absolutePath, 'r'),
                'filename' => basename($absolutePath),
            ],
        ];

        return (bool) $this->apiMultipart('setChatPhoto', $multipart);
    }

    /**
     * Low-level multipart call for uploading files to Telegram (e.g., setChatPhoto).
     * @param array<int,array{name:string,contents:mixed,filename?:string}> $multipart
     * @return mixed
     */
    private function apiMultipart(string $method, array $multipart): mixed
    {
        $segments = ['/bot'.$this->token.'/'];
        if ($this->useTestEnvironment) {
            $segments[] = 'test/';
        }
        $segments[] = ltrim($method, '/');
        $path = implode('', $segments);

        try {
            $response = $this->httpClient->post($path, [
                'http_errors' => false,
                'multipart' => $multipart,
            ]);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Telegram API request failed: '.$e->getMessage(), previous: $e);
        }

        $payload = json_decode((string) $response->getBody(), true) ?: [];

        if (!is_array($payload) || ($payload['ok'] ?? false) !== true) {
            $description = (string) Arr::get($payload, 'description', 'unknown error');
            $errorCode = (int) Arr::get($payload, 'error_code', 0);
            throw new RuntimeException("Telegram API error ({$errorCode}): {$description}");
        }

        return $payload['result'] ?? null;
    }

    /**
     * Send a text message.
     *
     * @param int|string $chatId
     * @param array<string,mixed> $options
     * @return array<string,mixed>
     */
    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'text' => $text,
        ], $options);

        return $this->api('sendMessage', $params);
    }

    /**
     * Edit message text.
     *
     * @param array<string,mixed> $options
     * @return array<string,mixed>
     */
    public function editMessageText(string $text, array $options): array
    {
        $params = array_merge(['text' => $text], $options);
        return $this->api('editMessageText', $params);
    }

    /**
     * Answer a callback query.
     *
     * @param array<string,mixed> $options
     * @return bool
     */
    public function answerCallbackQuery(string $callbackQueryId, array $options = []): bool
    {
        $params = array_merge(['callback_query_id' => $callbackQueryId], $options);
        return (bool) $this->api('answerCallbackQuery', $params);
    }

    /**
     * Send photo.
     *
     * @param int|string $chatId
     * @param array<string,mixed> $options
     * @return array<string,mixed>
     */
    public function sendPhoto(int|string $chatId, string $photo, array $options = []): array
    {
        $params = array_merge([
            'chat_id' => $chatId,
            'photo' => $photo,
        ], $options);

        return $this->api('sendPhoto', $params);
    }

    /**
     * getFile + build download URL.
     *
     * @return array{file_path?:string} Result with file info.
     */
    public function getFile(string $fileId): array
    {
        return $this->api('getFile', ['file_id' => $fileId]);
    }

    public function buildFileDownloadUrl(string $filePath): string
    {
        return sprintf('https://api.telegram.org/file/bot%s/%s', $this->token, ltrim($filePath, '/'));
    }

    // ============ Webhook management ============

    /**
     * Set webhook.
     *
     * @param array<string,mixed> $options
     * @return bool
     */
    public function setWebhook(string $url, array $options = []): bool
    {
        $params = array_merge(['url' => $url], $options);
        return (bool) $this->api('setWebhook', $params);
    }

    /**
     * Delete webhook.
     *
     * @return bool
     */
    public function deleteWebhook(bool $dropPendingUpdates = false): bool
    {
        return (bool) $this->api('deleteWebhook', ['drop_pending_updates' => $dropPendingUpdates]);
    }

    /**
     * Get webhook info.
     * @return array<string,mixed>
     */
    public function getWebhookInfo(): array
    {
        return $this->api('getWebhookInfo');
    }

    // ============ WebApp-specific helpers ============

    /**
     * Build an InlineKeyboard button that opens a WebApp.
     *
     * @return array{text:string,web_app:array{url:string}}
     */
    public function buildInlineWebAppButton(string $text, string $webAppUrl): array
    {
        return [
            'text' => $text,
            'web_app' => ['url' => $webAppUrl],
        ];
    }

    /**
     * Build reply_markup for an inline keyboard.
     * Provide rows of buttons (each button is an array).
     *
     * @param array<int,array<int,array<string,mixed>>> $rows
     * @return array<string,mixed>
     */
    public function buildInlineKeyboard(array $rows): array
    {
        return [
            'reply_markup' => [
                'inline_keyboard' => $rows,
            ],
        ];
    }

    /**
     * Set bot's menu button to open a WebApp (setChatMenuButton).
     *
     * @param int|string|null $chatId Specify chat_id to set per-chat; omit for default.
     * @return bool
     */
    public function setMenuButtonWebApp(string $buttonText, string $webAppUrl, int|string|null $chatId = null): bool
    {
        $params = [
            'menu_button' => [
                'type' => 'web_app',
                'text' => $buttonText,
                'web_app' => ['url' => $webAppUrl],
            ],
        ];

        if ($chatId !== null) {
            $params['chat_id'] = $chatId;
        }

        return (bool) $this->api('setChatMenuButton', $params);
    }

    /**
     * Answer a WebApp query (answerWebAppQuery) to post a message on behalf of the WebApp.
     *
     * @param array<string,mixed> $result InlineQueryResult-like object.
     * @return array<string,mixed>
     */
    public function answerWebAppQuery(string $webAppQueryId, array $result): array
    {
        return $this->api('answerWebAppQuery', [
            'web_app_query_id' => $webAppQueryId,
            'result' => $result,
        ]);
    }

    /**
     * Verify Telegram WebApp initData integrity per official docs.
     * See: https://core.telegram.org/bots/webapps#validating-data-received-via-the-web-app
     *
     * @param string|array<string,string> $initData Either raw query string or already-parsed map.
     */
    public function verifyWebAppInitData(string|array $initData): bool
    {
        $data = is_string($initData) ? $this->parseQueryString($initData) : $initData;

        $hash = $data['hash'] ?? '';
        if ($hash === '') {
            return false;
        }

        $dataCheck = $data;
        unset($dataCheck['hash']);

        ksort($dataCheck, SORT_STRING);

        $pairs = [];
        foreach ($dataCheck as $key => $value) {
            $pairs[] = $key.'='.$value;
        }
        $dataCheckString = implode("\n", $pairs);

        // secret_key = HMAC_SHA256(bot_token) with key 'WebAppData'
        // See docs: https://core.telegram.org/bots/webapps#validating-data-received-via-the-web-app
        $secretKey = hash_hmac('sha256', $this->token, 'WebAppData', true);
        $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($calculatedHash, $hash);
    }

    /**
     * Parse an URL-like query string into array without urldecode pitfalls for JSON values.
     *
     * @return array<string,string>
     */
    private function parseQueryString(string $query): array
    {
        // PHP's parse_str decodes '+' as space; we preserve values by using RFC3986 decoding.
        $result = [];
        foreach (explode('&', $query) as $chunk) {
            if ($chunk === '') {
                continue;
            }
            $parts = explode('=', $chunk, 2);
            $key = rawurldecode($parts[0]);
            $val = isset($parts[1]) ? rawurldecode($parts[1]) : '';
            $result[$key] = $val;
        }
        return $result;
    }

    // ============ Deep link builders (start/startattach) ============

    /**
     * Build a standard bot deep link with optional start parameter.
     * Example: https://t.me/botusername?start=YOUR_PARAM
     */
    public function buildStartLink(string $botUsername, ?string $startParam = null): string
    {
        $url = sprintf('https://t.me/%s', ltrim($botUsername, '@'));
        if ($startParam !== null && $startParam !== '') {
            $url .= '?start='.rawurlencode($startParam);
        }
        return $url;
    }

    /**
     * Build an attachment menu link.
     * Example: https://t.me/botusername?startattach=command
     * Optionally add choose=users+bots+groups+channels
     */
    public function buildStartAttachLink(string $botUsername, ?string $startAttachParam = null, array $choose = []): string
    {
        $query = [];
        $query['startattach'] = $startAttachParam ?? '';
        if (!empty($choose)) {
            $query['choose'] = implode('+', $choose);
        }

        $qs = http_build_query($query, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);
        $qs = trim($qs, '&=');
        $base = sprintf('https://t.me/%s', ltrim($botUsername, '@'));
        return $qs === '' ? $base.'?startattach' : $base.'?'.$qs;
    }

    /**
     * Build an attachment-menu link targeting a specific chat.
     * Example: https://t.me/username?attach=botusername&startattach=command
     */
    public function buildAttachInChatLink(string $chatUsernameOrInvite, string $botUsername, ?string $startAttachParam = null): string
    {
        $base = sprintf('https://t.me/%s', ltrim($chatUsernameOrInvite, '@'));
        $query = ['attach' => ltrim($botUsername, '@')];
        if ($startAttachParam !== null && $startAttachParam !== '') {
            $query['startattach'] = $startAttachParam;
        }
        $qs = http_build_query($query, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);
        return $base.'?'.$qs;
    }

    // ============ Convenience for Restaurant context ============

    /**
     * Send the restaurant's welcome message to a chat (if configured).
     *
     * @param int|string $chatId
     * @return array<string,mixed>|null
     */
    public function sendRestaurantWelcome(int|string $chatId): ?array
    {
        if ($this->restaurantContext === null) {
            return null;
        }
        $text = trim((string) $this->restaurantContext->welcome_message);
        if ($text === '') {
            return null;
        }
        return $this->sendMessage($chatId, $text);
    }
}

 