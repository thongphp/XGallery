<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands;

use App\Console\BaseCrawlerCommand;
use Exception;

/**
 * Class Batdongsan
 * @package App\Console\Commands
 */
final class Batdongsan extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batdongsan {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from Batdongsan.com.vn';

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

        // Process all pages
        $pages->each(function ($page) {
            if (!$page) {
                return;
            }
            $this->progressBarSetSteps($page->count());
            // Process items on page
            $page->each(function ($item) {
                $this->progressBarSetInfo($item['url']);
                $this->progressBarSetStatus('FETCHING');
                \App\Jobs\Batdongsan::dispatch($item['url']);
                $this->progressBarAdvanceStep();
                $this->progressBarSetStatus('QUEUED');
            });
            $this->progressBar->advance();
        });

        return true;
    }
}
