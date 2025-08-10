<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\TelegramBot;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class WebhookController extends Controller
{
    /**
     * Handle incoming Telegram updates for a specific restaurant.
     */
    public function handle(Request $request, Restaurant $restaurant)
    {
        $update = $request->all();

        // Basic /start greeting for private chats
        $message = $update['message'] ?? null;
        if (is_array($message) && ($message['chat']['type'] ?? '') === 'private') {
            $text = (string) ($message['text'] ?? '');
            if ($text === '/start' || str_starts_with($text, '/start ')) {
                $chatId = $message['chat']['id'];

                $greeting = "Привет! В этом приложении — всё самое важное о $restaurant->name: меню, фото, адрес, актуальные новости, анонсы событий и бронирование.\n\n"
                    . "Внутри приложения нажмите кнопку Repeat — это знак, что вы к нам вернетесь. И ваши друзья в Telegram увидят, что вам в $restaurant->name действительно понравилось.\n"
                    . "А ещё, скоро здесь можно будет оставить комментарий — и вы сможете прочитать отзывы только от ваших знакомых близких людей.\n\n"
                    . "🤩 Неужели теперь можно узнать, куда ходят друзья — даже не спрашивая их лично?! ";

                $bot = TelegramBot::forRestaurant($restaurant);
                $webAppUrl = $bot->buildRestaurantWebAppUrl($restaurant);

                $replyMarkup = [
                    'keyboard' => [[
                        [
                            'text' => 'в приложение',
                            'web_app' => ['url' => $webAppUrl],
                        ],
                    ]],
                    'resize_keyboard' => true,
                    // Set one_time_keyboard to false to keep the button visible under the input
                    'one_time_keyboard' => false,
                ];

                $bot->sendMessage($chatId, $greeting, [
                    'reply_markup' => $replyMarkup,
                ]);
            }
        }

        return response()->noContent(BaseResponse::HTTP_OK);
    }
}


