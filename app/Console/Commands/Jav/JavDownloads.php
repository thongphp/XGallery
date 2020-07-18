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
use App\Crawlers\Crawler\Onejav;
use App\Facades\UserActivity;
use App\Models\JavDownload;

/**
 * Process download pending JAV
 * @package App\Console\Commands
 */
final class JavDownloads extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:downloads {task=download}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download JAVs';

    protected function download()
    {
        $downloads = JavDownload::all();
        if ($downloads->isEmpty()) {
            return true;
        }

        $this->progressBarInit($downloads->count());
        $downloads->each(function ($download) {
            $this->progressBarSetInfo($download->item_number);
            $item = $download->downloads()->first();

            if (!$item) {
                return;
            }

            $crawler = app(Onejav::class);
            // Check again to get updated torrent link
            $item = $crawler->getItems($item->url)->first();
            $crawler->getClient()->download($item->torrent, 'onejav');
            $this->progressBarSetStatus('FINISHED');
            $download->forceDelete();

            UserActivity::notify('%s %s video ' . $item->title, null, 'download');
        });

        return true;
    }
}
