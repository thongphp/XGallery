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
use App\Models\Flickr\FlickrDownload;
use App\Models\Jav\JavMovie;
use App\Models\User;
use App\Observers\FlickrDownloadObserver;
use App\Observers\JavMovieObserver;
use App\Services\Client\FlickrClient;
use App\Services\Flickr\UrlExtractor;
use App\Services\Flickr\Validation;
use App\Services\GoogleDrive;
use App\Services\GooglePhoto;
use App\Services\UserActivity;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Http\Request;
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
            return new Validation;
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
        Totem::auth(static function (Request $request) {
            if (!$user = $request->user()) {
                return false;
            }

            /* @var User $user */
            return $user->isAdmin();
        });

        JavMovie::observe(JavMovieObserver::class);
        FlickrDownload::observe(FlickrDownloadObserver::class);
    }
}
