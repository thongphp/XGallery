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
 * Class FlickrContacts
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

    public function fully()
    {
        if (!$contacts = Flickr::get('contacts.getList')) {
            return false;
        }

        $this->output->note(
            sprintf('Got %d contacts in %d pages', $contacts->contacts->total, $contacts->contacts->pages)
        );

        $this->progressBarInit($contacts->contacts->pages);

        for ($page = 1; $page <= $contacts->contacts->pages; $page++) {
            // Add contacts on a page
            \App\Jobs\Flickr\FlickrContacts::dispatch($page);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }

        return true;
    }
}
