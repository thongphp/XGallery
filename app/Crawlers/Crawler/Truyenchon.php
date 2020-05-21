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
 * Class Truyenchon
 * @package App\Crawlers\Crawler
 */
final class Truyenchon extends AbstractCrawler
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
        $item->images = collect($crawler->filter('.page-chapter img')->each(function ($img) {
            return $img->attr('data-original');
        }));

        if ($crawler->filter('h1.txt-primary a')->count()) {
            $item->title = trim($crawler->filter('h1.txt-primary a')->text());
        } elseif ($crawler->filter('h1.txt-primary a')->count()) {
            $item->title = trim($crawler->filter('h1.title-detail')->text());
        }

        if ($crawler->filter('.detail-content p')->count()) {
            $item->description = $crawler->filter('.detail-content p')->text();
        }

        return $item;
    }

    /**
     * @param  string  $itemUri
     * @return Collection
     */
    public function getItemChapters(string $itemUri): ?Collection
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $nodes = $crawler->filter('.list-chapter ul li.row');

        if ($nodes->count() === 0) {
            return null;
        }

        return collect($crawler->filter('.list-chapter ul li.row .chapter a')->each(function ($node) {
            try {
                return $node->attr('href');
            } catch (Exception $exception) {
                return null;
            }
        }));
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

        $links = $crawler->filter('.ModuleContent .items .item')->each(function ($el) {
            return [
                'url' => $el->filter('.image a')->attr('href'),
                'cover' => $el->filter('img')->attr('data-original'),
                'title' => $el->filter('h3 a')->text(),
            ];
        });

        return collect($links);
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
            $pages = $crawler->filter('.pagination li a')->last()->attr('href');

            return (int) Url::fromString($pages)->getQueryParameter('page');
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
        $url = $this->buildUrl('/the-loai', $conditions);
        return $this->getIndexLinks($url);
    }
}
