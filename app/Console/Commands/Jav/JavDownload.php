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

/**
 * Process download pending JAV
 * @package App\Console\Commands
 */
class JavDownload extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:downloads {task} {item_number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download JAVs';

    protected function download()
    {
        $downloads = \App\Models\JavDownload::where(['is_downloaded' => null])->get();
        $this->progressBarInit($downloads->count());
        $downloads->each(function ($download) {
            \App\Jobs\Jav\JavDownload::dispatch($download);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        });

        return true;
    }

    protected function add()
    {
        $itemNumber = $this->argument('item_number');
        if (\App\Models\JavDownload::where(['item_number' => $itemNumber])->first()) {
            $this->output->warning('Already exists');
            return false;
        }

        $model = app(\App\Models\JavDownload::class);
        $model->item_number = $itemNumber;
        $model->save();

        $this->output->text('Item <fg=white;options=bold>'.$itemNumber.'</> added to queue');
        return true;
    }
}
