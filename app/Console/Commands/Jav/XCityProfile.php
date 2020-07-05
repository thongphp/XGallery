<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Jav;

use App\Console\BaseCrawlerCommand;
use Exception;

/**
 * Class XCity
 * @package App\Console\Commands
 */
final class XCityProfile extends BaseCrawlerCommand
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
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\XCityProfile::class)
            ->getItemLinks($endpoint->url . '&page=' . $endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            \App\Jobs\Jav\XCityProfile::dispatch($item);
            $this->progressBar->advance();
        });

        $endpoint->succeed()->save();

        return true;
    }
}
