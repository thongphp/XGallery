<?php

namespace App\Console\Commands\Flickr;

use App\Console\BaseCommand;
use App\Repositories\Flickr\PhotoRepository;

class FlickrPhotoSizes extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photossizes {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get photos sizes';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        // @todo Progressbar
        if (!$photos = app(PhotoRepository::class)->getPhotosWithNoSizes()) {
            return true;
        }

        foreach ($photos as $photo) {
            \App\Jobs\Flickr\FlickrPhotoSizes::dispatch($photo->id);
        }

        return true;
    }
}
