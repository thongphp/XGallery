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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MongoDB\BSON\UTCDateTime;

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
    protected $description = 'Fetching data from Onejav';

    /**
     * @return bool
     */
    protected function daily(): bool
    {
        return $this->indexProcess('https://onejav.com/'.date('Y/m/d'));
    }

    /**
     * Get links in a index page and process these
     * @param  string  $url
     * @return bool
     */
    private function indexProcess(string $url)
    {
        if (!$pages = $this->getCrawler()->getIndexLinks($url)) {
            return false;
        }

        $this->progressBarInit($pages->count());

        $pages->each(function ($items) {
            // Pages process
            $this->progressBarSetSteps($items->count());
            $this->itemsProcess($items);
            $this->progressBar->advance();
        });
        return true;
    }

    /**
     * Process a collection of items
     * @param  Collection  $items
     */
    private function itemsProcess(Collection $items)
    {
        if ($items->isEmpty()) {
            $this->progressBar->setMessage('There are no items', 'info');
            return;
        }

        $items->each(function ($item, $index) {
            $this->progressBarSetInfo($item['title']);
            // Convert to Mongo DateTime
            $originalItem = $item;
            if (isset($item['date']) && null !== $item['date']) {
                $item['date'] = new UTCDateTime($item['date']->getTimestamp() * 1000);
            }
            $this->insertItem($item);
            // Process to OneJAV to JavMovies with: Idols & Genres
            \App\Jobs\Jav\OneJav::dispatch($originalItem);
            $this->progressBarAdvanceStep();
            $this->progressBarSetStatus('QUEUED');
        });
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

        // For moment we can't use getIndexLinks because we are using recursive to get last page of this site
        $items = $this->getCrawler()->getItemLinks($endpoint->url.$endpoint->page);

        if (!$items || $items->isEmpty()) {
            $endpoint->failed = (int) $endpoint->failed + 1;
            if ($endpoint->failed === 10) {
                $endpoint->page = 1;
                $endpoint->failed = 0;
                $endpoint->save();
                return false;
            }

            $endpoint->page = (int) $endpoint->page + 1;
            $endpoint->save();
            return false;
        }

        $endpoint->page = (int) $endpoint->page + 1;
        $endpoint->save();

        $this->progressBarInit(1);
        $this->progressBar->setMessage($items->count(), 'steps');
        $this->itemsProcess($items);

        return true;
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        return app(\App\Models\Onejav::class);
    }
}
