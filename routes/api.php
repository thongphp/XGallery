<?php

use App\Http\Apis\Controllers\Batdongsan\BatdongsanController;
use App\Http\Apis\Controllers\Jav\JavController;
use App\Http\Apis\Controllers\Nhaccuatui\NhaccuatuiController;
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

Route::namespace('App\Http\Controllers\Apis')
    ->prefix('v1/github')
    ->group(function () {
        Route::any('/', [\App\Http\Apis\GithubController::class, 'webhook'])->name('webhook');
    });
