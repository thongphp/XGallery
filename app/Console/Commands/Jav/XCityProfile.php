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
class XCityProfile extends BaseCrawlerCommand
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
     * @return bool
     */
    public function daily(): bool
    {
        if (!$items = $this->getCrawler()->getItemLinks('https://xxx.xcity.jp/idol/')) {
            return false;
        }

        $this->progressBarInit($items->count());

        $items->each(function ($item) {
            $this->progressBarSetInfo($item['url']);
            $this->progressBarSetStatus('FETCHING');
            // Because this is daily request. We don't need use limit channel
            \App\Jobs\Jav\XCityProfile::dispatch($item);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        });

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$pages = $this->getIndexLinks()) {
            return false;
        }

        $this->progressBarInit($pages->count());

        // Process all pages. Actually one page
        $pages->each(function ($page) {
            $this->progressBar->setMessage($page->count(), 'steps');
            $this->progressBar->setMessage(0, 'step');
            // Process items on page
            $page->each(function ($item, $index) {
                $this->progressBarSetInfo($item['url']);
                $this->progressBarSetStatus('FETCHING');
                \App\Jobs\Jav\XCityProfile::dispatch($item);
                $this->progressBarAdvanceStep();
                $this->progressBarSetStatus('QUEUED');
            });
            $this->progressBar->advance();
        });

        return true;
    }
}
