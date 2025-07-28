<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AggregatorController;

/*
|--------------------------------------------------------------------------
| Web Routes - Restaurant Aggregator Platform
|--------------------------------------------------------------------------
|
*/

Route::get('/', [AggregatorController::class, 'site'])->name('site');

// Catch-all роут для Vue Router SPA (должен быть ДО account роута)
Route::get('/restaurant/{any?}', [AggregatorController::class, 'site'])->where('any', '.*')->name('site.restaurant');

Route::get('/account/{any?}', [AggregatorController::class, 'account'])->where('any', '.*')->name('account');

Route::get('/admin', [AggregatorController::class, 'admin'])->name('admin');
