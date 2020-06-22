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
use App\Jobs\Flickr\FlickrContact;
use App\Repositories\Flickr\ContactRepository;
use Exception;

/**
 * @package App\Console\Commands\Flickr
 */
final class FlickrContacts extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:contacts {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching Flickr contacts';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        try {
            $contacts = Flickr::getContactsOfCurrentUser();
        } catch (Exception $exception) {
            return false;
        }

        $this->output->note(
            sprintf('Got %d contacts in %d pages', $contacts->contacts->total, $contacts->contacts->pages)
        );

        $this->processContacts($contacts->contacts->contact);
        $this->progressBarInit($contacts->contacts->pages);
        $this->progressBarSetStatus('QUEUED');
        $this->progressBar->advance();

        if ($contacts->contacts->pages === 1) {
            return true;
        }

        for ($page = 2; $page <= $contacts->contacts->pages; $page++) {
            try {
                // @TODO It should be FlickrClient
                $nextContacts = Flickr::getContactsOfCurrentUser($page);
            } catch (Exception $exception) {
                return false;
            }

            $this->processContacts($nextContacts->contacts->contact);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }

        return true;
    }

    /**
     * @TODO Send batch to queues ( FLICK_CONTACT_BATCH_NUMBERS = 50 )
     * @param array $contacts
     */
    private function processContacts(array $contacts): void
    {
        $repository = app(ContactRepository::class);

        foreach ($contacts as $contact) {
            if ($repository->findOrCreateByNsId($contact->nsid)->isDone()) {
                continue;
            }

            FlickrContact::dispatch($contact->nsid);
        }
    }
}
