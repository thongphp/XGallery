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
use App\Jobs\Flickr\FlickrContactQueue;
use App\Jobs\Flickr\FlickrPhotoQueue;
use App\Models\Flickr\Contact;
use App\Repositories\Flickr\ContactRepository;
use App\Repositories\Flickr\PhotoRepository;
use Laminas\Hydrator\ObjectPropertyHydrator;

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
     * @return bool
     */
    public function fully(): bool
    {
        if (!$contacts = app(ContactRepository::class)->getItems(['sort-by' => 'updated_at', 'cache' => 0])) {
            return false;
        }

        foreach ($contacts as $contact) {
            /** @var Contact $contact */
            $contact->touch();

            $this->output->title('Working on contact '.$contact->nsid);

            $this->processContactPhotos($contact);
            $this->processContactFavouritePhotos($contact);
        }

        return true;
    }

    /**
     * @param \App\Models\Flickr\Contact $contact
     */
    private function processContactFavouritePhotos(Contact $contact): void
    {
        if (!$photos = Flickr::getFavouritePhotosOfUser($contact->nsid)) {
            return;
        }

        $this->output->note(
            sprintf("\t" . 'Got %d favourites photos in %d pages', $photos->photos->total, $photos->photos->pages)
        );

        $this->progressBarInit($photos->photos->pages);
        $this->processPhotos($photos->photos->photo, true);

        if ($photos->photos->pages === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = Flickr::getFavouritePhotosOfUser($contact->nsid, $page)) {
                continue;
            }

            $this->processPhotos($nextPhotos->photos->photo, true);

            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }
    }

    /**
     * @param \App\Models\Flickr\Contact $contact
     */
    private function processContactPhotos(Contact $contact): void
    {
        if (!$photos = Flickr::getUserPhotos($contact->nsid)) {
            return;
        }

        $this->output->note(
            sprintf("\t" . 'Got %d photos in %d pages', $photos->photos->total, $photos->photos->pages)
        );

        $this->progressBarInit($photos->photos->pages);
        $this->processPhotos($photos->photos->photo);

        if ($photos->photos->pages === 1) {
            return;
        }

        for ($page = 2; $page <= $photos->photos->pages; $page++) {
            if (!$nextPhotos = Flickr::getUserPhotos($contact->nsid, $page)) {
                continue;
            }

            $this->processPhotos($nextPhotos->photos->photo);

            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }
    }

    /**
     * @param array $photos
     * @param bool $shouldProcessOwner
     */
    private function processPhotos(array $photos, bool $shouldProcessOwner = false): void
    {
        $photoRepository = app(PhotoRepository::class);
        $contactRepository = app(ContactRepository::class);
        $hydrator = new ObjectPropertyHydrator();

        foreach ($photos as $photo) {
            /** @var \App\Models\Flickr\PhotoInterface $photo */
            $photoModel = $photoRepository->findOrCreateById($photo->id);

            if ($photoModel->isDone()) {
                continue;
            }

            $photoModel->fill($hydrator->extract($photo))
                ->save();

            FlickrPhotoQueue::dispatch($photoModel->id);

            if (false === $shouldProcessOwner
                || $contactRepository->findOrCreateByNsId($photoModel->owner)->isDone()) {
                continue;
            }

            FlickrContactQueue::dispatch($photoModel->owner);
        }
    }
}
