<?php

use App\Http\Apis\Batdongsan\BatdongsanController;
use App\Http\Controllers\Apis\Nhaccuatui\NhaccuatuiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('App\Http\Controllers\Apis\Nhaccuatui')
    ->prefix('v1/nhaccuatui')
    ->group(function () {
        Route::get('/', [NhaccuatuiController::class, 'index'])->name('nhaccuatui.dashboard.index');
        Route::get('/search', [NhaccuatuiController::class, 'search'])->name('nhaccuatui.songs.search');
        Route::put('/request', [NhaccuatuiController::class, 'request'])->name('nhaccuatui.console.request');
    });

Route::namespace('App\Http\Controllers\Apis\Batdongsan')
    ->prefix('v1/batdongsan')
    ->group(function () {
        Route::get('/', [BatdongsanController::class, 'index'])->name('batdongsan.dashboard.index');
    });
