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
use App\Jobs\Flickr\FlickrContactFavouritePhotos;
use App\Jobs\Flickr\FlickrContactPhotos;
use App\Repositories\Flickr\ContactRepository;

/**
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

    /**
     * @TODO Actually with current code we'll send thousand of jobs
     * @return bool
     */
    public function fully(): bool
    {
        if (!$contacts = app(ContactRepository::class)->getItems([
            'sort-by' => 'updated_at', 'cache' => 0
        ])) {
            return false;
        }

        $total = $contacts->total();
        $this->output->note(sprintf('Got %d contacts', $total));
        $this->progressBarInit($total);
        /**
         * @TODO Nothing queue yet !
         */
        $this->progressBarSetStatus('QUEUED');
        $this->progressBar->setMessage('Contacts', 'message');

        foreach ($contacts as $contact) {
            FlickrContactPhotos::dispatch($contact->nsid);
            FlickrContactFavouritePhotos::dispatch($contact->nsid);
            $this->progressBar->advance();
        }

        $this->progressBar->finish();

        return true;
    }
}
