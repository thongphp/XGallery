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
use App\Facades\FlickrClient;
use App\Repositories\Flickr\ContactRepository;

/**
 * Get and store Flickr' contacts of authorized user
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
    protected $description = 'Get Flickr contacts of authorized user';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        $contacts = FlickrClient::getContactsOfCurrentUser();

        $this->output->note(
            sprintf('Got %d contacts in %d pages', $contacts->contacts->total, $contacts->contacts->pages)
        );

        $this->progressBarInit($contacts->contacts->pages);
        $this->processContacts($contacts->contacts->contact);
        $this->progressBar->advance();

        if ($contacts->contacts->pages === 1) {
            return true;
        }

        for ($page = 2; $page <= $contacts->contacts->pages; $page++) {
            $nextContacts = FlickrClient::getContactsOfCurrentUser($page);

            $this->processContacts($nextContacts->contacts->contact);
            $this->progressBar->advance();
        }

        return true;
    }

    /**
     * Store array of contact into database with NSID only
     *
     * @param array $contacts
     */
    private function processContacts(array $contacts): void
    {
        $repository = app(ContactRepository::class);
        $this->progressBarSetSteps(count($contacts));

        foreach ($contacts as $index => $contact) {
            $this->progressBarSetStep($index + 1);
            $repository->findOrCreateByNsId($contact->nsid);
            $this->progressBarSetStatus('FINISHED');
        }
    }
}
