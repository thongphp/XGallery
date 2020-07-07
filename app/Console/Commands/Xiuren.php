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
use App\Models\XiurenModel;

/**
 * Class Xiuren
 * @package App\Console\Commands
 */
final class Xiuren extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xiuren {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from http://www.xiuren.org/';

    /**
     * @return bool
     * @throws \Exception
     */
    public function fully(): bool
    {
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $crawler = app(\App\Crawlers\Crawler\Xiuren::class);
        $items = app(\App\Crawlers\Crawler\Xiuren::class)->getItemLinks($endpoint->url.'/page-'.$endpoint->page.'.html');

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) use ($crawler) {
            $itemDetail = $crawler->getItem($item['url']);
            XiurenModel::updateOrCreate(['url' => $item['url']], ['images' => $itemDetail->images] + $item);
            $this->progressBar->advance();
        });

        $endpoint->succeed()->save();

        return true;
    }
}
