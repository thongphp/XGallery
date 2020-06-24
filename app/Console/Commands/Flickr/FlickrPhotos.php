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
    protected $description = 'Fetching Flickr photos of a contact';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        if (!$contact = app(ContactRepository::class)->getContactWithoutPhotos()) {
            // @todo If found nothing then reset photo_state
            return false;
        }

        $contact->photo_state = 1;
        $contact->save();
        $this->output->note('Working on contact: '.$contact->nsid);

        FlickrContactPhotos::dispatch($contact->nsid);
        FlickrContactFavouritePhotos::dispatch($contact->nsid);

        return true;
    }
}
