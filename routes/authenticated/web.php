<?php

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

use App\Http\Controllers\Config\ConfigController;
use App\Http\Controllers\Config\UserConfigController;
use App\Http\Controllers\Flickr\FlickrController;
use App\Http\Controllers\Jav\JavController;
use App\Http\Controllers\KissGoddess\KissGoddessController;
use App\Http\Controllers\Truyenchon\TruyenchonController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Xiuren\XiurenController;
use App\Services\UserRole;

Route::middleware(['auth'])
    ->namespace(UserController::class)
    ->group(
        static function () {
            Route::get('/profile', [UserController::class, 'profile'])->name('user.profile.view');
            Route::get('/activities', [UserController::class, 'activities'])->name('user.activities.view');
            Route::get('/logout', [UserController::class, 'logout'])->name('user.logout');
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_JAV_DOWNLOAD])
    ->namespace(JavController::class)
    ->prefix('jav')
    ->group(
        static function () {
            Route::post('download/{itemNumber}', [JavController::class, 'download'])->name('jav.download.request');
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_XIUREN_DOWNLOAD])
    ->namespace(XiurenController::class)
    ->prefix('xiuren')
    ->group(
        static function () {
            Route::post('download/{id}', [XiurenController::class, 'download'])->name('xiuren.download.request');
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_KISSGODDESS_DOWNLOAD])
    ->namespace(KissGoddessController::class)
    ->prefix('kissgoddess')
    ->group(
        static function () {
            Route::post('download/{id}', [KissGoddessController::class, 'download'])->name(
                'kissgoddess.download.request'
            );
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_KISSGODDESS_DOWNLOAD])
    ->namespace(TruyenchonController::class)
    ->prefix('truyenchon')
    ->group(
        static function () {
            Route::post('download/{id}', [TruyenchonController::class, 'download'])->name(
                'truyenchon.download.request'
            );
            Route::post('re-download/{id}', [TruyenchonController::class, 'reDownload'])->name(
                'truyenchon.re-download.request'
            );
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_FLICKR_DOWNLOAD])
    ->namespace(FlickrController::class)
    ->prefix('flickr')
    ->group(
        static function () {
            Route::post('download', [FlickrController::class, 'download'])->name('flickr.download.request');
        }
    );

Route::middleware(['auth'])
    ->namespace(FlickrController::class)
    ->prefix('flickr')
    ->group(
        static function () {
            Route::get('/', [FlickrController::class, 'dashboard'])->name('flickr.dashboard.view');
            Route::post('/', [FlickrController::class, 'dashboard'])->name('flickr.dashboard.view');
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_ADMIN_CONFIG])
    ->namespace(ConfigController::class)
    ->group(
        static function () {
            Route::post('/config', [ConfigController::class, 'store'])->name(
                'config.global.view'
            );
            Route::get('/config', [ConfigController::class, 'show'])->name(
                'config.global.view'
            );
        }
    );

Route::middleware(['auth', 'permission:'.UserRole::PERMISSION_USER_CONFIG])
    ->namespace(UserConfigController::class)
    ->group(
        static function () {
            Route::post('/user-config', [UserConfigController::class, 'store'])->name('config.user.view');
            Route::get('/user-config', [UserConfigController::class, 'show'])->name('config.user.view');
        }
    );
