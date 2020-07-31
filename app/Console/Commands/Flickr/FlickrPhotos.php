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
use App\Models\Flickr\FlickrContact;
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
        $contactRepository = app(ContactRepository::class);

        if (!$contact = $contactRepository->getContactWithoutPhotos()) {
            $contactRepository->resetPhotoStates();
            $this->output->note('Reset photos state of all contacts');
            $contact = $contactRepository->getContactWithoutPhotos();
        }

        $this->output->note(sprintf('Working on %s contact', $contact->nsid));
        $contact->{FlickrContact::KEY_PHOTO_STATE} = 1;
        $contact->save();

        /**
         * Follow SOLID we are not going update FlickrContact in this command
         */
//        $this->progressBarInit(1);
//        \App\Jobs\Flickr\FlickrContact::dispatch($contact);
//        $this->progressBarSetStatus('QUEUED');

        FlickrContactPhotos::withChain([
            new FlickrContactFavouritePhotos($contact)
        ])->dispatch($contact);

        return true;
    }
}
