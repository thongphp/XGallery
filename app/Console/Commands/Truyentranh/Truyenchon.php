<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Truyentranh;

use App\Console\BaseCrawlerCommand;
use App\Models\Truyentranh\TruyenchonModel;
use Exception;

/**
 * Class Truyenchon
 * @package App\Console\Commands\Truyentranh
 */
final class Truyenchon extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyentranh:truyenchon {task=fully} {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching stories from http://truyenchon.com/';


    /**
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\Truyenchon::class)->getStories($endpoint->url . '/?page=' . $endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            TruyenchonModel::firstOrCreate(['url'], $item);
            $this->progressBar->advance();
        });

        $endpoint->succeed()->save();

        return true;
    }
}
