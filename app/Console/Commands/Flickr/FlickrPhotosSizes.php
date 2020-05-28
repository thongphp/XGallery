<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Flickr;

use App\Console\BaseCommand;
use App\Jobs\Flickr\FlickrPhotoSizes;

/**
 * Class FlickrPhotosSizes
 * @package App\Console\Commands\Flickr
 */
final class FlickrPhotosSizes extends BaseCommand
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
    protected $description = 'Fetching Flickr photos';

    public function fully()
    {
        if (!$photos = \App\Models\FlickrPhotos::where(['sizes' => null])->take(30)->get()) {
            return false;
        }

        $this->progressBarInit(30);
        $this->progressBarSetMessage('Photos');

        foreach ($photos as $photo) {
            FlickrPhotoSizes::dispatch($photo);
            $this->progressBarSetInfo($photo->id);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }

        return true;
    }
}
