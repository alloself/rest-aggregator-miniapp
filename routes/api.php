<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Account\CategoryController;
use App\Http\Controllers\Account\RestaurantController;
use App\Http\Controllers\Account\DishController;
use App\Http\Controllers\Account\FileController;
use App\Http\Controllers\Site\RestaurantController as SiteRestaurantController;
use App\Http\Controllers\Account\NewsController;
use App\Http\Controllers\Account\EventController;
use App\Http\Controllers\Telegram\WebhookController as TelegramWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

$accountResources = [
    'categories' => CategoryController::class,
    'restaurants' => RestaurantController::class,
    'files' => FileController::class,
    'news' => NewsController::class,
    'events' => EventController::class,
    'dishes' => DishController::class,
];



Route::prefix('account')->middleware(['auth:sanctum', 'restaurant.team', 'account.permission'])->group(function () use ($accountResources) {
    Route::get('me', [AuthController::class, 'me']);
    Route::apiResources($accountResources);

    Route::prefix('destroy')->group(function () use ($accountResources) {
        foreach ($accountResources as $route => $controller) {
            Route::post($route, [$controller, 'deleteMany']);
        }
    });
});

Route::prefix('site')->group(function () {
    Route::get('restaurants/{slug}', [SiteRestaurantController::class, 'show']);
    Route::get('restaurants/{slug}/news', [SiteRestaurantController::class, 'news']);
    Route::get('restaurants/{slug}/events', [SiteRestaurantController::class, 'events']);
    Route::get('restaurants/{slug}/events/{eventSlug}', [SiteRestaurantController::class, 'event']);
});

Route::post('telegram/webhook/{restaurant}', [TelegramWebhookController::class, 'handle']);
