<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Account\CategoryController;
use App\Http\Controllers\Account\RestaurantController;

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
];

Route::middleware(['auth:sanctum'])->get('me', [AuthController::class, 'me']);

Route::prefix('account')->middleware(['auth:sanctum', 'role:restaurant|root'])->group(function () use ($accountResources) {

    Route::apiResources($accountResources);

    Route::prefix('destroy')->group(function () use ($accountResources) {
        foreach ($accountResources as $route => $controller) {
            Route::post($route, [$controller, 'deleteMany']);
        }
    });
});
