<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AggregatorController;

/*
|--------------------------------------------------------------------------
| Web Routes - Restaurant Aggregator Platform
|--------------------------------------------------------------------------
|
*/

Route::get('/account/{any?}', [AggregatorController::class, 'account'])->where('any', '.*')->name('account');

Route::get('/admin', [AggregatorController::class, 'admin'])->name('admin');

Route::get('/', [AggregatorController::class, 'site'])->name('site');
