<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Providers;

use App\Facades\UserActivity as UserActivityFacade;
use App\Services\Flickr\UrlExtractor;
use App\Services\FlickrClient;
use App\Services\FlickrValidate;
use App\Services\GoogleDrive;
use App\Services\GooglePhoto;
use App\Services\UserActivity;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Studio\Totem\Totem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->app->environment() === 'local') {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->bind('googledrive', function () {
            return new GoogleDrive;
        });

        $this->app->bind('googlephoto', function () {
            return new GooglePhoto;
        });

        $this->app->bind('flickr', function () {
            return new FlickrClient;
        });

        $this->app->bind('flickrvalidate', function () {
            return new FlickrValidate;
        });

        $this->app->bind('flickrurlextractor', function () {
            return new UrlExtractor;
        });

        $this->app->bind(UserActivityFacade::class, function () {
            return new UserActivity;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Totem::auth(function ($request) {
            $user = $request->user();
            $authenticatedUsers = config('services.authenticated.emails');
            return in_array($user->email, $authenticatedUsers);
        });
    }
}
