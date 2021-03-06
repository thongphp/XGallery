<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Jav\IdolsController;
use App\Http\Controllers\Jav\JavController;
use App\Http\Controllers\KissGoddess\KissGoddessController;
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
    ->group(
        static function () {
            Route::get('flickr', [\App\Http\Controllers\Auth\FlickrController::class, 'oauth']);
            Route::get('flickr/callback', [\App\Http\Controllers\Auth\FlickrController::class, 'callback']);
            Route::get('google', [GoogleController::class, 'oauth'])->name('oauth.login');
            Route::get('google/callback', [GoogleController::class, 'callback']);
        }
    );

Route::namespace(JavController::class)
    ->prefix('jav')
    ->group(
        static function () {
            Route::match(['GET', 'POST'], '/', [JavController::class, 'dashboard'])->name('jav.dashboard.view');
            Route::get('movie/{id}', [JavController::class, 'movie'])->name('jav.movie.view');
            Route::get('genre/{id}', [JavController::class, 'genre'])->name('jav.genre.view');
            Route::match(['GET', 'POST'], '/idols', [IdolsController::class, 'idols'])->name(
                'jav.idols.dashboard.view'
            );
            Route::get('idol/{id}', [IdolsController::class, 'idol'])->name('jav.idol.view');
        }
    );

Route::namespace(XiurenController::class)
    ->prefix('xiuren')
    ->group(
        static function () {
            Route::match(['GET', 'POST'], '/', [XiurenController::class, 'dashboard'])->name(
                'xiuren.dashboard.view'
            );
            Route::get('{id}', [XiurenController::class, 'item'])->name('xiuren.item.view');
            Route::post('download/{id}', [XiurenController::class, 'download'])->name('xiuren.download.request');
        }
    );

Route::namespace(KissGoddessController::class)
    ->prefix('kissgoddess')
    ->group(
        static function () {
            Route::match(['GET', 'POST'], '/', [KissGoddessController::class, 'dashboard'])->name(
                'kissgoddess.dashboard.view'
            );
            Route::get('{id}', [KissGoddessController::class, 'item'])->name('kissgoddess.item.view');
        }
    );

Route::namespace(TruyenchonController::class)
    ->prefix('truyenchon')
    ->group(
        static function () {
            Route::match(['GET', 'POST'], '/', [TruyenchonController::class, 'dashboard'])->name(
                'truyenchon.dashboard.view'
            );
            Route::get('{id}/{chapter}', [TruyenchonController::class, 'story'])->name('truyenchon.story.view');
            Route::post('search', [TruyenchonController::class, 'search'])->name('truyenchon.search.view');
        }
    );
