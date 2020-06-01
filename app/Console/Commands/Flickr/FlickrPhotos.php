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
use App\Facades\Flickr;

/**
 * Class FlickrPhotos
 * @package App\Console\Commands\Flickr
 */
final class FlickrPhotos extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:photos {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching Flickr photos';

    public function fully()
    {
        if (!$contact = app(\App\Repositories\FlickrContacts::class)->getItems(
            [
                'sort-by' => 'updated_at', 'cache' => 0
            ])->first()) {
            return false;
        }

        $contact->touch();

        $this->output->title('Working on contact '.$contact->nsid);

        if (!$photos = Flickr::get('people.getPhotos', ['user_id' => $contact->nsid])) {
            return false;
        }

        $this->output->note(
            sprintf('Got %d photos in %d pages', $photos->photos->total, $photos->photos->pages)
        );

        $this->progressBarInit($photos->photos->pages);

        // Trigger job to fetch photos of user
        for ($page = 1; $page <= $photos->photos->pages; $page++) {
            \App\Jobs\Flickr\FlickrPhotos::dispatch($contact, $page);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }

        return true;
    }
}
