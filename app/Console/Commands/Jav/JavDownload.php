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

/**
 * Process download pending JAV
 * @package App\Console\Commands
 */
final class JavDownload extends BaseCommand
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
        $downloads = \App\Models\JavDownload::where(['is_downloaded' => null])->get();
        if ($downloads->isEmpty()) {
            return true;
        }

        $download = $downloads->first()->downloads()->first();
        $crawler = app(Onejav::class);
        $item = $crawler->getItems($download->url)->first();
        $crawler->getClient()->download($item->torrent, 'onejav');

        return true;
    }
}
