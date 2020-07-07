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
use App\Models\KissgoddessModel;

/**
 * Class Kissgoddess
 * @package App\Console\Commands
 */
final class Kissgoddess extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kissgoddess {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from https://kissgoddess.com/gallery/';

    /**
     * @return bool
     * @throws \Exception
     */
    public function fully(): bool
    {
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $crawler = app(\App\Crawlers\Crawler\Kissgoddess::class);
        $items = app(\App\Crawlers\Crawler\Kissgoddess::class)->getItemLinks($endpoint->url.'/'.$endpoint->page.'.html');

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) use ($crawler) {
            $itemDetail = $crawler->getItem($item['url']);
            KissgoddessModel::updateOrCreate(['url' => $item['url']], ['images' => $itemDetail->images]);
            $this->progressBar->advance();
        });

        $endpoint->succeed()->save();

        return true;
    }
}
