<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;

Route::get('/', function () {
    return view('welcome');
});

// Telegram Bot Routes
Route::prefix('telegram')->group(function () {
    // Webhook для получения обновлений от Telegram
    Route::post('/webhook', [TelegramBotController::class, 'webhook'])->name('telegram.webhook');
    
    // Управление webhook
    Route::get('/webhook/set', [TelegramBotController::class, 'setWebhook'])->name('telegram.webhook.set');
    Route::get('/webhook/info', [TelegramBotController::class, 'getWebhookInfo'])->name('telegram.webhook.info');
    Route::get('/webhook/delete', [TelegramBotController::class, 'deleteWebhook'])->name('telegram.webhook.delete');
});
