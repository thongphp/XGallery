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
 * Class XCityVideoModel
 * @package App\Console\Commands
 */
final class XCityVideo extends BaseCommand
{
    use HasCrawler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:xcityvideo {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching video data from XCity';

    /**
     * @SuppressWarnings("unused")
     *
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\XCityVideo::class)
            ->getItemLinks($endpoint->url . '&page=' . $endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            \App\Jobs\Jav\XCityVideo::dispatch($item);
            $this->progressBarSetInfo($item);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        });

        $endpoint->succeed()->save();

        return true;
    }
}
