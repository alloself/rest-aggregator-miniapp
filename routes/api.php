<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\DishController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\AuthController;

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

// Аутентификация (публичные маршруты)
Route::prefix('v1/auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    // Защищенные маршруты аутентификации
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::get('tokens', [AuthController::class, 'tokens']);
        Route::delete('tokens/{token_id}', [AuthController::class, 'revokeToken']);
    });
    
    // Администраторские маршруты создания пользователей
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('create-restaurant-owner', [AuthController::class, 'createRestaurantOwner']);
        Route::post('create-admin', [AuthController::class, 'createAdmin']);
    });
});

// Публичные маршруты для Telegram Mini Apps
Route::prefix('v1')->group(function () {
    
    // Рестораны (только чтение)
    Route::get('restaurants', [RestaurantController::class, 'index']);
    Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show']);
    Route::get('restaurants/{restaurant}/menu', [RestaurantController::class, 'menu']);
    Route::get('restaurants/{restaurant}/events', [RestaurantController::class, 'events']);
    
    // Меню (только чтение)
    Route::get('menus', [MenuController::class, 'index']);
    Route::get('menus/{menu}', [MenuController::class, 'show']);
    
    // Блюда (только чтение)
    Route::get('dishes', [DishController::class, 'index']);
    Route::get('dishes/{dish}', [DishController::class, 'show']);
    Route::get('dishes/category/{categoryId}', [DishController::class, 'byCategory']);
    
    // Категории (только чтение)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    
    // События (только чтение)
    Route::get('events', [EventController::class, 'index']);
    Route::get('events/{event}', [EventController::class, 'show']);
    
    // Новости (только чтение)
    Route::get('news', [NewsController::class, 'index']);
    Route::get('news/{news}', [NewsController::class, 'show']);
    
    // Система лайков (работает без аутентификации через telegram_user_id)
    Route::prefix('likes')->group(function () {
        Route::post('toggle', [LikeController::class, 'toggle']);
        Route::get('count', [LikeController::class, 'getLikes']);
        Route::get('user', [LikeController::class, 'getUserLikes']);
        Route::post('check', [LikeController::class, 'checkLikes']);
        Route::get('most-liked', [LikeController::class, 'mostLiked']);
    });
});

// Защищенные маршруты для владельцев ресторанов
Route::middleware(['auth:sanctum'])->prefix('v1/restaurant')->group(function () {
    
    // Управление собственным рестораном
    Route::get('profile', [RestaurantController::class, 'profile']);
    Route::patch('profile', [RestaurantController::class, 'updateProfile']);
    
    // Управление меню (ограничено собственным рестораном)
    Route::middleware('check.restaurant.owner')->group(function () {
        Route::apiResource('menus', MenuController::class)->except(['index', 'show']);
        Route::apiResource('dishes', DishController::class)->except(['index', 'show']);
        Route::patch('dishes/{dish}/toggle-availability', [DishController::class, 'toggleAvailability']);
        Route::post('dishes/{dish}/attach-categories', [DishController::class, 'attachCategories']);
        Route::post('dishes/{dish}/detach-categories', [DishController::class, 'detachCategories']);
        
        Route::apiResource('events', EventController::class)->except(['index', 'show']);
        Route::apiResource('news', NewsController::class)->except(['index', 'show']);
    });
});

// Админские маршруты (полный доступ)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('v1/admin')->group(function () {
    
    // Полное управление ресторанами
    Route::apiResource('restaurants', RestaurantController::class);
    Route::patch('restaurants/{restaurant}/toggle-active', [RestaurantController::class, 'toggleActive']);
    
    // Управление категориями
    Route::apiResource('categories', CategoryController::class);
    
    // Полное управление контентом
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('dishes', DishController::class);
    Route::apiResource('events', EventController::class);
    Route::apiResource('news', NewsController::class);
    
    // Модерация контента
    Route::prefix('moderation')->group(function () {
        Route::get('dishes/pending', [DishController::class, 'pending']);
        Route::patch('dishes/{dish}/approve', [DishController::class, 'approve']);
        Route::patch('dishes/{dish}/reject', [DishController::class, 'reject']);
        
        Route::get('events/pending', [EventController::class, 'pending']);
        Route::patch('events/{event}/approve', [EventController::class, 'approve']);
        Route::patch('events/{event}/reject', [EventController::class, 'reject']);
        
        Route::get('news/pending', [NewsController::class, 'pending']);
        Route::patch('news/{news}/approve', [NewsController::class, 'approve']);
        Route::patch('news/{news}/reject', [NewsController::class, 'reject']);
    });
    
    // Аналитика
    Route::prefix('analytics')->group(function () {
        Route::get('restaurants', [RestaurantController::class, 'analytics']);
        Route::get('dishes/popular', [DishController::class, 'popular']);
        Route::get('events/statistics', [EventController::class, 'statistics']);
        Route::get('likes/trends', [LikeController::class, 'trends']);
    });
});