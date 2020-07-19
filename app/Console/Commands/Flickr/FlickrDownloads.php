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
use App\Jobs\Flickr\FlickrDownloadPhotoToLocal;
use App\Models\Flickr\FlickrDownload;

/**
 * Get and push a Flickr' contact to queue for getting detail
 * @package App\Console\Commands\Flickr
 */
final class FlickrDownloads extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:downloads {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download requested';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        $photos = FlickrDownload::limit(100)->get();
        $photos->each(function ($photo) {
            FlickrDownloadPhotoToLocal::dispatch($photo->photo_id, $photo->google_album_id);
            $photo->delete();
        });

        return true;
    }
}
