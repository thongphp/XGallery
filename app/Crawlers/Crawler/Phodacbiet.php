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
 * Class Phodacbiet
 * @package App\Crawlers\Crawler
 */
class Phodacbiet extends AbstractCrawler
{
    public function getItemDetail(string $itemUri): ?object
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = new stdClass;
        $item->url = $itemUri;
        $item->images = collect($crawler->filter('.bbWrapper img.bbImage')->each(
            function ($el) {
                return $el->attr('src');
            }
        ))->reject(function ($value) {
            return null === $value;
        })->toArray();

        return $item;
    }

    /**
     * @param  string  $indexUri
     * @return int
     */
    public function getIndexPagesCount(string $indexUri): int
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        try {
            return (int) $crawler->filter('ul.pageNav-main li.pageNav-page ')->last()->text();
        } catch (Exception $exception) {
            return 1;
        }
    }

    public function search(array $conditions): ?Collection
    {
        return collect([]);
    }

    public function getItemLinks(string $indexUri = null): ?Collection
    {
        return $this->getPosts($indexUri);
    }

    /**
     * @param  string  $forumLink
     * @return Collection|null
     */
    protected function getPosts(string $forumLink): ?Collection
    {
        if (!$crawler = $this->crawl($forumLink)) {
            return null;
        }

        try {
            return collect(
                $crawler->filter('.threadList .cate.post.thread a')->each(
                    function ($anchor) {
                        return [
                            'url' => 'https://phodacbiet.info'.$anchor->attr('href'),
                            'title' => $anchor->attr('title'),
                        ];
                    }
                )
            );
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param  Url  $url
     * @param  int  $page
     * @return string
     */
    protected function buildUrlWithPage(Url $url, int $page): string
    {
        return $this->buildUrl($url->getPath().'page-'.$page);
    }
}
