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
use App\Jobs\DownloadNhacCuaTui;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Nhaccuatui
 * @package App\Console\Commands
 */
class Nhaccuatui extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nhaccuatui {task=search} {download=0} {--title=} {--singer=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from Nhaccuatui';

    /**
     * @return bool
     */
    protected function search(): bool
    {
        // We will get all data of result. It would take time
        $pages = $this->getCrawler()->search([
            'title' => $this->option('title'),
            'singer' => $this->option('singer')
        ]);

        if (!$pages) {
            return false;
        }

        $this->progressBarInit($pages->count());

        $pages->each(function ($items, $key) {
            $this->progressBarSetSteps($items->count());
            $items->each(function ($item, $key) {
                $this->progressBarSetInfo($item['name']);

                if ($this->argument('download') == 1) {
                    DownloadNhacCuaTui::dispatch($item['url']);
                    $this->progressBarSetStatus('QUEUED');
                }

                // Item process
                $this->insertItem($item);
                $this->progressBarAdvanceStep();
                $this->progressBarSetStatus('COMPLETED');
            });

            $this->progressBar->advance();
        });

        return true;
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(\App\Models\Nhaccuatui::class);
    }
}
