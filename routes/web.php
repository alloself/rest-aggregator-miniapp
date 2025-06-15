<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;

/*
|--------------------------------------------------------------------------
| Web Routes - Restaurant Aggregator Platform
|--------------------------------------------------------------------------
|
*/

Route::get('/', function () {
    return view('site');
})->name('site.index');