<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Providers;

use App\Services\Flickr;
use App\Services\GoogleDrive;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('googledrive', function () {
            return new GoogleDrive;
        });

        $this->app->bind('flickr', function () {
            return new Flickr;
        });

        $this->app->bind('flickr\urlextractor', function () {
            return new Flickr\UrlExtractor;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
