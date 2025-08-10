<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Services\TelegramBot;
use Throwable;

class RestaurantObserver
{
    /**
     * Sync bot first name with restaurant name when saved.
     * We use 'saved' instead of 'updated' to ensure the slug has been properly updated by HasSlug trait.
     */
    public function saved(Restaurant $restaurant): void
    {
        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            return;
        }
        
        $tokenChanged = $restaurant->wasChanged('telegram_bot_token');
        $wasRecentlyCreated = $restaurant->wasRecentlyCreated;

        try {
            $bot = TelegramBot::forRestaurant($restaurant);

            try {
                if ($tokenChanged && !$wasRecentlyCreated) {
                    $bot->deleteWebhook(true);
                }
            } catch (Throwable $e) {
                report($e);
            }
            $bot->setupRestaurantMiniApp($restaurant);

            $bot->setMyName($restaurant->name);
            // Refresh menu button URL to reflect the new slug after name change
            try {
                $bot->setupRestaurantMiniApp($restaurant);
            } catch (Throwable $e) {
                report($e);
            }
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
        } catch (Throwable $e) {
            report($e);
        }
    }
}
