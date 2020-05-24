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
 * R18 only used to get videos. There are no idol information
 * @package App\Console\Commands
 */
class R18 extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:r18 {task=fully} {--url=}';

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
        if (!$pages = $this->getIndexLinks()) {
            return false;
        }

        if ($pages->isEmpty()) {
            return false;
        }

        $this->progressBar = $this->createProgressBar();
        $this->progressBar->setMaxSteps($pages->count());

        // Process all pages. Actually one page
        $pages->each(function ($page) {
            $this->progressBar->setMessage($page->count(), 'steps');
            $this->progressBar->setMessage(0, 'step');
            // Process items on page
            $page->each(function ($item, $index) {
                $this->progressBar->setMessage($item['url'], 'info');
                $this->progressBar->setMessage('FETCHING', 'status');
                \App\Jobs\Jav\R18::dispatch($item);
                $this->progressBar->setMessage($index + 1, 'step');
                $this->progressBar->setMessage('QUEUED', 'status');
            });
            $this->progressBar->advance();
        });

        return true;
    }

    /**
     * Keep update R18 daily beside fully
     * @return bool
     */
    public function daily(): bool
    {
        $uri = 'https://www.r18.com/videos/vod/movies/list/pagesize=60/price=all/sort=new/type=all/page=1';
        if (!$items = $this->getCrawler()->getItemLinks($uri)) {
            return false;
        }

        $this->progressBar = $this->createProgressBar();
        $this->progressBar->setMaxSteps($items->count());

        $items->each(function ($item) {
            $this->progressBar->setMessage($item['url'], 'info');
            $this->progressBar->setMessage('FETCHING', 'status');
            \App\Jobs\Jav\R18::dispatch($item);
            $this->progressBar->setMessage('QUEUED', 'status');
            $this->progressBar->advance();
        });

        return true;
    }
}
