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
 * Class Kissgoddess
 * @package App\Crawlers\Crawler
 */
final class Kissgoddess extends AbstractCrawler
{
    /**
     * @param  string  $itemUri
     * @return object|null
     */
    public function getItemDetail(string $itemUri): ?object
    {
        $pages = $this->getIndexPagesCount($itemUri);
        $item = new stdClass;
        $item->url = $itemUri;

        $itemUri = str_replace('.html', '', $itemUri);
        for ($page = 1; $page <= $pages; $page++) {
            $crawler = $this->crawl($itemUri.'_'.$page.'.html');

            if (!$crawler) {
                continue;
            }

            $item->images[$page] = collect($crawler->filter('.td-gallery-content img')->each(
                function ($image) {
                    return $image->attr('src');
                }
            ))->reject(function ($value) {
                return null === $value;
            })->toArray();
        }

        return $item;
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
            $count = $crawler->filter('#pages a')->count();
            $pages = $crawler->filter('#pages a');
            $page = $pages->getNode($count - 2);
            $page = explode('/', $page->getAttribute('href'));
            $page = explode('_', end($page));
            $page = end($page);

            return (int) str_replace('.html', '', $page);
        } catch (Exception $exception) {
            return 1;
        }
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

        return collect($crawler->filter('.td-module-image .td-module-thumb a')->each(
            function ($el) {
                return [
                    'url' => 'https://kissgoddess.com'.$el->attr('href'),
                    'title' => $el->attr('title'),
                    'cover' => $el->filter('img')->attr('src'),
                ];
            }
        ));
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
        return $this->buildUrl($url->getPath().$page.'.html');
    }
}
