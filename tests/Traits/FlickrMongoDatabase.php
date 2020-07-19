<?php

namespace Tests\Traits;

use App\Models\Flickr\FlickrContactModel;
use App\Models\Flickr\FlickrDownload;
use App\Models\Flickr\FlickrPhotoModel;

trait FlickrMongoDatabase
{
    public function cleanUpFlickrMongoDb(): void
    {
        app(FlickrContactModel::class)->newModelQuery()->forceDelete();
        app(FlickrPhotoModel::class)->newModelQuery()->forceDelete();
        app(FlickrDownload::class)->newModelQuery()->forceDelete();
    }
}
