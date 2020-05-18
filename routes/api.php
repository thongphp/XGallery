<?php

use App\Http\Controllers\Nhaccuatui\NhaccuatuiController;
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

Route::namespace('App\Http\Controllers\Nhaccuatui')
    ->prefix('v1/nhaccuatui')
    ->group(function () {
        Route::get('/', [NhaccuatuiController::class, 'index'])->name('nhaccuatui.dashboard.index');
        Route::get('/songs', [NhaccuatuiController::class,'getSongs'])->name('nhaccuatui.songs.index');
        Route::put('/fetch', [NhaccuatuiController::class,'fetchSongs'])->name('nhaccuatui.console.fetch');
    });
