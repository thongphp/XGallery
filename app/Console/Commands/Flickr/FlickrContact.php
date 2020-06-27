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
use App\Models\Flickr\FlickrContactModel;
use App\Repositories\Flickr\ContactRepository;

/**
 * Get and push a Flickr' contact to queue for getting detail
 * @package App\Console\Commands\Flickr
 */
final class FlickrContact extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:contact {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Flickr contact detail';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        $contactRepository = app(ContactRepository::class);

        if (!$contact = $contactRepository->getItemByConditions([
            'sort-by' => 'updated_at', FlickrContactModel::KEY_STATE => null, 'cache' => 0
        ])) {
            $contactRepository->resetStates();
            $this->output->note('Reset state of all contacts');

            $contact = $contactRepository->getItemByConditions([
                'sort-by' => 'updated_at', FlickrContactModel::KEY_STATE => null, 'cache' => 0
            ]);
        }

        $contact->touch();
        $this->output->note(sprintf('Working on %s contact', $contact->nsid));
        \App\Jobs\Flickr\FlickrContact::dispatch($contact->nsid);

        return true;
    }
}
