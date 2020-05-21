<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use Exception;
use Illuminate\Support\Collection;
use Spatie\Url\Url;
use stdClass;

/**
 * Class Xiuren
 * @package App\Services\Crawler
 */
final class Xiuren extends AbstractCrawler
{

    /**
     * @param  string  $itemUri
     * @return object|null
     */
    public function getItemDetail(string $itemUri): ?object
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = new stdClass;
        $item->url = $itemUri;
        $item->images = collect($crawler->filter('#main .post .photoThum a')->each(
            function ($a) {
                return $a->attr('href');
            }
        ))->reject(function ($value) {
            return null === $value;
        })->toArray();

        return $item;
    }

    /**
     * @param  string|null  $indexUri
     * @return Collection
     */
    public function getItemLinks(string $indexUri = null): ?Collection
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return null;
        }

        return collect($crawler->filter('#main .loop .content a')->each(
            function ($el) {
                return [
                    'url' => $el->attr('href'),
                    'cover' => $el->filter('img')->attr('src'),
                ];
            }
        ));
    }

    /**
     * @param  string  $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri = null): int
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        try {
            $pages = explode('/', $crawler->filter('#page .info')->text());

            return (int) end($pages);
        } catch (Exception $exception) {
            return 1;
        }
    }

    /**
     * @param  array  $conditions
     * @return Collection|null
     */
    public function search(array $conditions = []): ?Collection
    {
        return null;
    }

    /**
     * @param  Url  $url
     * @param  int  $page
     * @return string
     */
    protected function buildUrlWithPage(Url $url, int $page): string
    {
        return $this->buildUrl($url->getPath().'page-'.$page.'.html');
    }
}
