<?php

namespace App\Console\Commands\Flickr;

use App\Console\BaseCommand;
use App\Repositories\Flickr\PhotoRepository;

/**
 * Class FlickrPhotoSizes
 * @package App\Console\Commands\Flickr
 */
final class FlickrPhotoSizes extends BaseCommand
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
        $photos = app(PhotoRepository::class)->getPhotosWithNoSizes();

        if ($photos->isEmpty()) {
            $this->output->note('There are no photos without sizes');
            return true;
        }

        $this->output->note(sprintf('Working on %d photos', $photos->count()));

        $this->progressBarInit($photos->count());
        $this->progressBarSetMessage('Photos', 'message');

        foreach ($photos as $photo) {
            \App\Jobs\Flickr\FlickrPhotoSizes::dispatch($photo);
            $this->progressBarSetInfo($photo->id);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }

        return true;
    }
}
