<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Services\TelegramBot;
use Throwable;

class RestaurantObserver
{
    /**
     * Sync bot first name with restaurant name when updated.
     */
    public function updated(Restaurant $restaurant): void
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            return;
        }

        $nameChanged = $restaurant->wasChanged('name');
        $descChanged = $restaurant->wasChanged('welcome_message') || $restaurant->wasChanged('subtitle');

        if (!$nameChanged && !$descChanged) {
            return;
        }

        try {
            $bot = TelegramBot::forRestaurant($restaurant);

            if ($nameChanged) {
                $bot->setMyName($restaurant->name);
            }

            if ($descChanged) {
                // Prefer long welcome_message; fallback to subtitle for short welcome_message
                $long = (string) ($restaurant->welcome_message ?? '');
                $shortSource = trim((string) ($restaurant->subtitle ?? ''));
                if ($shortSource === '' && $long !== '') {
                    // build a concise short welcome_message from long
                    $shortSource = trim(mb_substr(strip_tags($long), 0, 120));
                }

                if ($long !== '') {
                    $bot->setMyDescription($long);
                }

                if ($shortSource !== '') {
                    $bot->setMyShortDescription($shortSource);
                }
            }

            $bot->setupRestaurantMiniApp($restaurant);


        } catch (Throwable $e) {
            report($e);
        }
    }
}


