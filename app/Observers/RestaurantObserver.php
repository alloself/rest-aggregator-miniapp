<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Services\TelegramBotSetupService;

/**
 * Observer для модели Restaurant.
 * Очищает настройки Telegram-бота при удалении ресторана.
 * Обновление настроек бота выполняется по кнопке через API.
 */
class RestaurantObserver
{
    public function deleting(Restaurant $restaurant): void
    {
        app(TelegramBotSetupService::class)->cleanupBot($restaurant);
    }
}
