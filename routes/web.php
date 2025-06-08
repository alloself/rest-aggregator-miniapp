<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;

/*
|--------------------------------------------------------------------------
| Web Routes - Restaurant Aggregator Platform
|--------------------------------------------------------------------------
|
| Маршруты для трех интерфейсов:
| - Site (публичный): /
| - Account (рестораны): /account
| - Admin (админ): /admin
|
*/

// Site Interface (публичный интерфейс)
Route::get('/', function () {
    return view('site');
})->name('site.index');

Route::prefix('restaurant')->name('site.restaurant.')->group(function () {
    Route::get('/{slug}', function ($slug) {
        return view('site', compact('slug'));
    })->name('show');
    
    Route::get('/{slug}/menu', function ($slug) {
        return view('site', compact('slug'));
    })->name('menu');
    
    Route::get('/{slug}/events', function ($slug) {
        return view('site', compact('slug'));
    })->name('events');
});

Route::prefix('category')->name('site.category.')->group(function () {
    Route::get('/{slug}', function ($slug) {
        return view('site', compact('slug'));
    })->name('show');
});

Route::get('/favorites', function () {
    return view('site');
})->name('site.favorites');

Route::get('/search', function () {
    return view('site');
})->name('site.search');

// Account Interface (личный кабинет ресторанов)
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/', function () {
        return view('account');
    })->name('dashboard');
    
    Route::get('/login', function () {
        return view('account');
    })->name('login');
    
    Route::get('/register', function () {
        return view('account');
    })->name('register');
    
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/profile', function () {
            return view('account');
        })->name('profile');
        
        Route::get('/menu', function () {
            return view('account');
        })->name('menu');
        
        Route::get('/events', function () {
            return view('account');
        })->name('events');
        
        Route::get('/analytics', function () {
            return view('account');
        })->name('analytics');
        
        Route::get('/settings', function () {
            return view('account');
        })->name('settings');
    });
});

// Admin Interface (административная панель)
Route::prefix('admin')->name('admin.')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', function () {
        return view('admin');
    })->name('dashboard');
    
    Route::get('/restaurants', function () {
        return view('admin');
    })->name('restaurants');
    
    Route::get('/users', function () {
        return view('admin');
    })->name('users');
    
    Route::get('/analytics', function () {
        return view('admin');
    })->name('analytics');
    
    Route::get('/content', function () {
        return view('admin');
    })->name('content');
    
    Route::get('/settings', function () {
        return view('admin');
    })->name('settings');
});

// Telegram Bot Webhook (если используется)
Route::post('/telegram/webhook', [TelegramBotController::class, 'webhook'])
    ->name('telegram.webhook');

// Health Check для мониторинга
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'environment' => config('app.env'),
        'services' => [
            'database' => 'ok', // здесь можно добавить реальные проверки
            'redis' => 'ok',
            'telegram' => 'ok'
        ]
    ]);
})->name('health');

// Fallback route для SPA routing
Route::fallback(function () {
    // Определяем интерфейс по URL
    $path = request()->path();
    
    if (str_starts_with($path, 'account')) {
        return view('account');
    }
    
    if (str_starts_with($path, 'admin')) {
        return view('admin');
    }
    
    // По умолчанию возвращаем site интерфейс
    return view('site');
});
