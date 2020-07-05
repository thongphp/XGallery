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
use App\Jobs\Jav\UpdateGenres;
use App\Jobs\Jav\UpdateIdols;
use App\Models\Jav\JavMovieModel;
use App\Models\Jav\OnejavModel;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class Onejav
 * @package App\Console\Commands\Jav
 */
final class Onejav extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:onejav {task=fully} {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from OnejavModel';

    /**
     * @return bool
     */
    protected function daily(): bool
    {
        $crawler = app(\App\Crawlers\Crawler\Onejav::class);
        return $this->itemsProcess($crawler->getDaily());
    }

    /**
     * Process to get all OneJav data
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\Onejav::class)->getItems($endpoint->url.$endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->itemsProcess($items);
        $endpoint->succeed()->save();

        return true;
    }

    /**
     * @param  Collection  $items
     * @return bool
     */
    private function itemsProcess(Collection $items)
    {
        if ($items->isEmpty()) {
            return true;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            $attributes = $item->getAttributes();
            $item = OnejavModel::updateOrCreate(['url' => $attributes['url']], $attributes);
            $movie = JavMovieModel::updateOrCreate(
                ['item_number' => $item->title],
                [
                    'item_number' => $item->title,
                    'release_date' => $item->date,
                    'is_downloadable' => true,
                    'description' => $item->description
                ]
            );

            UpdateGenres::dispatch($movie, $item->tags);
            UpdateIdols::dispatch($movie, $item->actresses);
        });

        return true;
    }
}
