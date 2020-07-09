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
use App\Console\Traits\HasCrawler;
use Exception;

/**
 * @description R18 only used to get videos detail. Idol with name only
 * @package App\Console\Commands
 */
final class R18 extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:r18 {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from R18';

    /**
     * @return bool
     * @throws Exception
     */
    public function fully(): bool
    {
        if (!$endpoint = $this->getEndpoint('R18')) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\R18::class)->getItemLinks($endpoint->url . '/page=' . $endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            \App\Jobs\Jav\R18::dispatch($item);
            $this->progressBarSetInfo($item);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        });

        $this->progressBarFinished();
        $endpoint->succeed()->save();

        return true;
    }
}
