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
        if (!$restaurant->wasChanged('name')) {
            return;
        }

        $token = (string) $restaurant->telegram_bot_token;
        if ($token === '') {
            return;
        }

        try {
            TelegramBot::forRestaurant($restaurant)->setMyName($restaurant->name);
        } catch (Throwable $e) {
            // Avoid breaking the update flow; log for diagnosis.
            report($e);
        }
    }
}


