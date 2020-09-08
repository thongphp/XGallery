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

/**
 * Class Kissgoddess
 * @package App\Crawlers\Crawler
 */
final class Kissgoddess extends AbstractCrawler
{
    const BASE_URL = 'https://kissgoddess.com';

    /**
     * @param string $itemUri
     *
     * @return \App\Models\KissGoddess|null
     */
    public function getItem(string $itemUri): ?\App\Models\KissGoddess
    {
        $pages = $this->getIndexPagesCount($itemUri);
        $item = app(\App\Models\KissGoddess::class);
        $item->url = $itemUri;
        $images = collect([]);

        $itemUri = str_replace('.html', '', $itemUri);

        for ($page = 1; $page <= $pages; $page++) {
            $crawler = $this->crawl($itemUri . '_' . $page . '.html');

            if (!$crawler) {
                continue;
            }

            $images = $images->merge($crawler->filter('.td-gallery-content img')->each(
                function ($image) {
                    return $image->attr('src');
                }
            ));
        }

        $item->images = $images->toArray();

        return $item;
    }

    /**
     * @param string|null $indexUri
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
                    'url' => self::BASE_URL . $el->attr('href'),
                    'title' => $el->attr('title'),
                    'cover' => $el->filter('img')->attr('src') ?? $el->filter('img')->attr('data-original'),
                ];
            }
        ));
    }

    /**
     * @param string $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri): int
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

            return (int)str_replace('.html', '', $page);
        } catch (Exception $exception) {
            return 1;
        }
    }
}
