<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

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

        // Подгружаем ТОЛЬКО тех друзей, которые лайкнули этот ресторан
        $user->load(['friends' => function ($q) use ($restaurant) {
            $q->whereExists(function ($sq) use ($restaurant) {
                $sq->selectRaw('1')
                    ->from('likes')
                    ->whereColumn('likes.user_id', 'users.id')
                    ->where('likes.likeable_type', \App\Models\Restaurant::class)
                    ->where('likes.likeable_id', $restaurant->id);
            });
        }]);

        // liked_by_me признак
        $likedByMe = \App\Models\Like::query()
            ->where('user_id', $user->id)
            ->where('likeable_type', \App\Models\Restaurant::class)
            ->where('likeable_id', $restaurant->id)
            ->exists();

        $user->setAttribute('liked_by_me', $likedByMe);

        return response()->json($user);
    }

    /**
     * Проверка подписи initData по спецификации Telegram Mini Apps.
     */
    private function verifyInitData(string $initData, string $botToken): bool
    {
        parse_str($initData, $data);

        $hash = (string) ($data['hash'] ?? '');
        unset($data['hash']);

        ksort($data, SORT_STRING);

        $dataCheckStringParts = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            $dataCheckStringParts[] = $key . '=' . $value;
        }
        $dataCheckString = implode("\n", $dataCheckStringParts);

        // secret_key = HMAC_SHA256("WebAppData", bot_token)
        $secretKey = hash_hmac('sha256', 'WebAppData', $botToken, true);
        $calculatedHash = bin2hex(hash_hmac('sha256', $dataCheckString, $secretKey, true));

        return hash_equals($calculatedHash, strtolower($hash));
    }
}


