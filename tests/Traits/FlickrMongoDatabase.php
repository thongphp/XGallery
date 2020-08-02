<?php

namespace Tests\Traits;

use App\Models\Flickr\FlickrContact;
use App\Models\Flickr\FlickrDownload;
use App\Models\Flickr\FlickrPhoto;

trait FlickrMongoDatabase
{
    public function cleanUpFlickrMongoDb(): void
    {
        app(FlickrContact::class)->newModelQuery()->forceDelete();
        app(FlickrPhoto::class)->newModelQuery()->forceDelete();
        app(FlickrDownload::class)->newModelQuery()->forceDelete();
    }
}
