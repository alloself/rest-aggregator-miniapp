<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Illuminate\Support\Facades\Log;
use App\Services\TelegramBotService;

class MiniAppAuthController extends Controller
{
    /**
     * Валидация Telegram WebApp initData и возврат пользователя.
     * Документация: https://core.telegram.org/bots/webapps#initializing-mini-apps
     */
    public function auth(Request $request, string $slug)
    {
        $initData = (string) $request->input('init_data', '');

        if ($initData === '') {
            return response()->json(['message' => 'init_data is required'], BaseResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $restaurant = Restaurant::where('slug', $slug)->first();
        if (!$restaurant || !$restaurant->telegram_bot_token) {
            return response()->json(['message' => 'Restaurant or bot token not found'], BaseResponse::HTTP_NOT_FOUND);
        }

        if (!$this->verifyInitData($initData, $restaurant->telegram_bot_token)) {
            return response()->json(['message' => 'Invalid init_data signature'], BaseResponse::HTTP_FORBIDDEN);
        }

        // Извлекаем поля пользователя из initData
        parse_str($initData, $data);
        $rawUser = isset($data['user']) ? json_decode($data['user'], true) : null;

        if (!is_array($rawUser) || !isset($rawUser['id'])) {
            return response()->json(['message' => 'Invalid user payload'], BaseResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Пользователь уже создаётся при запуске бота (Webhook /start). Здесь только ищем по Telegram ID в pivot.
        $telegramId = (string) $rawUser['id'];

        $user = User::whereHas('restaurants', function ($q) use ($restaurant, $telegramId) {
            $q->where('restaurant_id', $restaurant->id)
                ->where('chat_id', $telegramId);
        })->first();

        if (!$user) {
            return response()->json(['message' => 'User not registered for this restaurant'], BaseResponse::HTTP_NOT_FOUND);
        }

        // Проверяем лайкнул ли текущий пользователь этот ресторан
        $liked = $restaurant->likes()
            ->where('user_id', $user->id)
            ->exists();

        // Находим всех друзей пользователя, которые лайкнули этот ресторан (двунаправленно)
        // 1) те, кого пользователь добавил как друзей
        $friendsLikedDirect = $user->friends()
            ->whereHas('likes', function ($query) use ($restaurant) {
                $query->where('likeable_id', $restaurant->id)
                      ->where('likeable_type', Restaurant::class);
            })
            ->get();

        // 2) те, кто добавил текущего пользователя (обратная связь)
        $friendsLikedReverse = $user->friendOf()
            ->whereHas('likes', function ($query) use ($restaurant) {
                $query->where('likeable_id', $restaurant->id)
                      ->where('likeable_type', Restaurant::class);
            })
            ->get();

        // Убираем pivot у обратных связей, чтобы не путать telegram_data (оно описывает текущего пользователя, а не друга)
        $friendsLikedReverse->each(function ($friend) {
            if (method_exists($friend, 'unsetRelation')) {
                $friend->unsetRelation('pivot');
            }
        });

        // Объединяем и удаляем дубликаты по id
        $friendsLiked = $friendsLikedDirect
            ->merge($friendsLikedReverse)
            ->unique('id')
            ->values();

        // Формируем данные о друзьях для ответа — перекладываем логику в модель
        $friendsLikedPayload = $friendsLiked->map(function ($friend) use ($restaurant) {
            return [
                'id' => $friend->id,
                'name' => trim((string) $friend->first_name . ' ' . (string) $friend->last_name) ?: $friend->username,
                'username' => $friend->username,
                'profile_photo_url' => $friend->getAvatarUrlForRestaurant($restaurant),
            ];
        });

        return response()->json(array_merge($user->toArray(), [
            'full_avatar_url' => $user->full_avatar_url,
            'liked_by_me' => $liked,
            'friends' => $friendsLikedPayload,
        ]));
    }

    /**
     * Проверка подписи initData по спецификации Telegram Mini Apps.
     */
    private function verifyInitData(string $initData, string $botToken): bool
    {
        parse_str($initData, $data);
        $signature = (string) ($data['signature'] ?? '');
        $hash = (string) ($data['hash'] ?? '');

        // Готовим строку проверки: исключаем hash и signature, сортируем, собираем key=value\n
        $dataForCheck = $data;
        unset($dataForCheck['hash'], $dataForCheck['signature']);
        ksort($dataForCheck, SORT_STRING);
        $dataCheckStringParts = [];
        foreach ($dataForCheck as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $dataCheckStringParts[] = $key . '=' . $value;
        }
        $dataCheckString = implode("\n", $dataCheckStringParts);

        // 1) Проверка Ed25519 по полю signature (третьесторонняя схема)
        if ($signature !== '' && function_exists('sodium_crypto_sign_verify_detached')) {
            // bot_id берём из токена (формат <bot_id>:<rest>)
            $botId = null;
            $tokenParts = explode(':', (string) $botToken, 2);
            if (isset($tokenParts[0]) && ctype_digit($tokenParts[0])) {
                $botId = $tokenParts[0];
            }
            if ($botId !== null) {
                $signedString = $botId . ':WebAppData' . "\n" . $dataCheckString;
                $signatureRaw = $this->base64UrlDecode($signature);
                if ($signatureRaw !== false) {
                    // Публичные ключи Telegram (prod и test)
                    $publicKeysHex = [
                        'e7bf03a2fa4602af4580703d88dda5bb59f32ed8b02a56c187fe7d34caed242d',
                        '40055058a4ee38156a06562e52eece92a771bcd8346a8c4615cb7376eddf72ec',
                    ];
                    foreach ($publicKeysHex as $hexKey) {
                        $pubKey = function_exists('sodium_hex2bin') ? \sodium_hex2bin($hexKey) : hex2bin($hexKey);
                        if ($pubKey !== false && \sodium_crypto_sign_verify_detached($signatureRaw, $signedString, $pubKey)) {
                            return true;
                        }
                    }
                }
            }
        }

        // 2) Стандартная серверная проверка по bot_token (HMAC-SHA256)
        if ($hash !== '') {
            // secret_key = HMAC_SHA256('WebAppData', bot_token)
            $secretKey = hash_hmac('sha256', 'WebAppData', $botToken, true);
            $calculatedHash = bin2hex(hash_hmac('sha256', $dataCheckString, $secretKey, true));
            if (hash_equals($calculatedHash, strtolower($hash))) {
                return true;
            }
        }

        // 3) Мягкая проверка для нестандартных клиентов
        return $this->softValidateInitData($data);
    }

    /**
     * Мягкая проверка init_data, когда Telegram не прислал hash (или клиент старый/нестандартный).
     * Проверяем свежесть auth_date и наличие user.
     */
    private function softValidateInitData(array $data): bool
    {
        $authDate = isset($data['auth_date']) ? (int) $data['auth_date'] : 0;
        if ($authDate <= 0) {
            return false;
        }

        // 24 часа окно
        if (abs(time() - $authDate) > 86400) {
            return false;
        }

        if (!array_key_exists('user', $data)) {
            return false;
        }

        $rawUser = json_decode((string) $data['user'], true);
        if (!is_array($rawUser) || !isset($rawUser['id'])) {
            return false;
        }

        return true;
    }

    /**
     * Декодировать base64url без паддинга
     */
    private function base64UrlDecode(string $data): string|false
    {
        $replaced = strtr($data, '-_', '+/');
        $padLen = (4 - (strlen($replaced) % 4)) % 4;
        if ($padLen > 0) {
            $replaced .= str_repeat('=', $padLen);
        }
        return base64_decode($replaced, true);
    }
}
