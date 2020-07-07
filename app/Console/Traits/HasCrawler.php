<?php

namespace App\Console\Traits;

use App\Repositories\CrawlerEndpoints;
use Exception;
use Illuminate\Database\Eloquent\Model;

trait HasCrawler
{
    /**
     * @return \App\Models\CrawlerEndpoints
     * @throws Exception
     */
    protected function getCrawlerEndpoint(): \App\Models\CrawlerEndpoints
    {
        /**
         * @var Model $endpoint
         */
        if (!$endpoint = app(CrawlerEndpoints::class)->getWorkingItem($this->getShortClassname())) {
            throw new Exception('Crawler endpoint not found');
        }

        if ((int) $endpoint->page === 0) {
            $endpoint->page = 1;
        }

        $this->output->note('Endpoint '.$endpoint->url.' with page '.$endpoint->page);

        return $endpoint;
    }
}
