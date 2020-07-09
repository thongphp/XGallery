<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Jav;

use App\Console\BaseCommand;
use Exception;

/**
 * Class XCity
 * @package App\Console\Commands
 */
final class XCityProfile extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcityprofile {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching profile data from XCity';

    /**
     * @SuppressWarnings("unused")
     *
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$endpoint = $this->getEndpoint('XCityProfile')) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\XCityProfile::class)
            ->getItemLinks($endpoint->url.'&page='.$endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            $this->output->warning('There are no items to process');
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            \App\Jobs\Jav\XCityProfile::dispatch($item);
            $this->progressBarSetInfo($item);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        });

        $this->progressBarFinished();
        $endpoint->succeed()->save();

        return true;
    }
}
