<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Flickr\FlickrController;
use App\Http\Controllers\Jav\IdolsController;
use App\Http\Controllers\Jav\JavController;
use App\Http\Controllers\Truyenchon\TruyenchonController;
use App\Http\Controllers\Xiuren\XiurenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard.dashboard.view')->middleware([]);

Route::namespace('App\Http\Controllers\Auth')
    ->prefix('oauth')
    ->group(function () {
        Route::get('flickr', [\App\Http\Controllers\Auth\FlickrController::class, 'oauth']);
        Route::get('flickr/callback', [\App\Http\Controllers\Auth\FlickrController::class, 'callback']);
        Route::get('google', [GoogleController::class, 'oauth'])->name('oauth.login');
        Route::get('google/callback', [GoogleController::class, 'callback']);
    });

Route::middleware(['auth'])->namespace(JavController::class)
    ->prefix('jav')
    ->group(function () {
        Route::match(['GET', 'POST'], '/', [JavController::class, 'dashboard'])->name('jav.dashboard.view');
        Route::get('movie/{id}', [JavController::class, 'movie'])->name('jav.movie.view');
        Route::get('genre/{id}', [JavController::class, 'genre'])->name('jav.genre.view');
        Route::match(['GET', 'POST'], '/idols', [IdolsController::class, 'idols'])->name('jav.idols.dashboard.view');
        Route::get('idol/{id}', [IdolsController::class, 'idol'])->name('jav.idol.view');
        Route::post('download/{itemNumber}', [JavController::class, 'download'])->name('jav.download.request');
    });

Route::middleware(['auth'])->namespace(XiurenController::class)
    ->prefix('xiuren')
    ->group(function () {
        Route::get('/', [XiurenController::class, 'dashboard'])->name('xiuren.dashboard.view');
        Route::get('{id}', [XiurenController::class, 'item'])->name('xiuren.item.view');
        Route::post('download/{id}', [XiurenController::class, 'download'])->name('xiuren.download.request');
    });

Route::middleware(['auth'])->namespace(TruyenchonController::class)
    ->prefix('truyenchon')
    ->group(function () {
        Route::get('/', [TruyenchonController::class, 'dashboard'])->name('truyenchon.dashboard.view');
        Route::get('{id}/{chapter}', [TruyenchonController::class, 'story'])->name('truyenchon.story.view');
        Route::post('search', [TruyenchonController::class, 'search'])->name('truyenchon.search.view');
        Route::post('download/{id}', [TruyenchonController::class, 'download'])->name('truyenchon.download.request');
    });

Route::middleware(['auth'])->namespace(FlickrController::class)
    ->prefix('flickr')
    ->group(function () {
        Route::get('/', [FlickrController::class, 'dashboard'])->name('flickr.dashboard.view');
        Route::post('/', [FlickrController::class, 'dashboard'])->name('flickr.dashboard.view');
        Route::post('download', [FlickrController::class, 'download'])->name('flickr.download.request');
    });
