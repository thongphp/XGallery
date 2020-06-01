<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console;

use App\Crawlers\Crawler\CrawlerInterface;
use App\Repositories\CrawlerEndpoints;
use App\Traits\HasObject;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class BaseCrawlerCommand
 * @package App\Console
 */
class BaseCrawlerCommand extends BaseCommand
{
    use HasObject;

    protected CrawlerInterface $crawler;
    protected Model            $model;

    /**
     * Process WHOLE site by specific Index URL
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$pages = $this->getIndexLinks()) {
            return false;
        }

        $this->progressBarInit($pages->count());

        // Process all pages
        $pages->each(function ($page) {
            if (!$page) {
                return;
            }
            $this->progressBarSetSteps($page->count());
            // Process items on page
            $page->each(function ($item, $index) {
                $this->progressBarSetInfo($item['url']);
                $this->progressBarSetStatus('FETCHING');
                /**
                 * @TODO Use Job instead directly
                 */
                if (!$itemDetail = $this->getCrawler()->getItemDetail($item['url'])) {
                    $this->progressBarAdvanceStep();
                    $this->progressBarSetStatus('FAILED');
                    return;
                }
                $data = get_object_vars($itemDetail);
                if (isset($item['cover'])) {
                    $data['cover'] = $item['cover'];
                }
                $this->insertItem($data);
                $this->progressBarAdvanceStep();
                $this->progressBarSetStatus('COMPLETED');
            });
            $this->progressBar->advance();
        });

        return true;
    }

    /**
     * @return bool|Collection
     * @throws Exception
     */
    protected function getIndexLinks()
    {
        if (!$endpoint = $this->getCrawlerEndpoint()) {
            return false;
        }

        $from = $to = (int) $endpoint->page;
        $pages = $this->getCrawler()->getIndexLinks($endpoint->url, $from, $to);

        if (!$pages->isEmpty()) {
            $endpoint->page = $to + 1;
            $endpoint->save();

            return $pages;
        }

        $this->error('Can not get index links');
        $endpoint->failed = (int) $endpoint->failed + 1;

        if ($endpoint->failed === 10) {
            $endpoint->page = 1;
            $endpoint->failed = 0;
            $endpoint->save();
            return false;
        }

        $endpoint->page = $to + 1;
        $endpoint->save();
        return false;
    }

    /**
     * @return Model|null
     * @throws Exception
     */
    protected function getCrawlerEndpoint()
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

    /**
     * @return CrawlerInterface
     */
    protected function getCrawler(): CrawlerInterface
    {
        if (isset($this->crawler)) {
            return $this->crawler;
        }

        $this->crawler = app('\\App\\Crawlers\\Crawler\\'.$this->getShortClassname());

        return $this->crawler;
    }

    /**
     * @param  array  $data
     * @param  bool  $isNew
     * @return Model
     */
    protected function insertItem(array $data, &$isNew = false)
    {
        $model = $this->getModel();

        /**
         * @var Model $item
         */
        if ($item = $model->getItemByUrl($data['url'])) {
            $item->touch();
            $isNew = true;
            return $item;
        }

        // Can not use fill() because it will be required fillable properties
        foreach ($data as $key => $value) {
            if (empty($value) || is_null($value)) {
                continue;
            }
            $model->{$key} = $value;
        }

        $model->save();

        return $model;
    }

    /**
     * @return Model
     */
    protected function getModel(): Model
    {
        $this->model = app('\App\\Models\\'.$this->getShortClassname());

        return $this->model;
    }

    /**
     * Process specific daily index page
     * @return bool
     */
    protected function daily(): bool
    {
        return false;
    }

    /**
     * Process specific Index URL
     * @return bool
     */
    protected function index(): bool
    {
        return false;
    }

    /**
     * Process an item only
     * @return bool
     */
    protected function item(): bool
    {
        return false;
    }

    /**
     * @return string|null
     */
    protected function getOptionUrl(): ?string
    {
        if (!$url = $this->option('url')) {
            $url = $this->ask('Please enter URL');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $url;
    }
}
