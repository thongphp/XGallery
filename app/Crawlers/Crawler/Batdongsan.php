<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Url\Url;
use stdClass;

/**
 * Class Batdongsan
 * @package App\Crawlers\Crawler
 */
final class Batdongsan extends AbstractCrawler
{
    const CRAWLER_ENDPOINT = 'https://batdongsan.com.vn';

    /**
     * @param  string  $itemUri
     * @return object|null
     */
    public function getItemDetail(string $itemUri): ?object
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = new stdClass();
        $nameNode = $crawler->filter('.pm-title h1');

        if ($nameNode->count() === 0) {
            $this->getLogger()->warning('Can not get title');

            return null;
        }

        $item->url = $itemUri;
        $item->name = trim($crawler->filter('.pm-title h1')->text(null, false));
        $item->price = trim($crawler->filter('.gia-title.mar-right-15 strong')->text(null, false));
        $item->size = trim($crawler->filter('.gia-title')->nextAll()->filter('strong')->text(null, false));
        $item->content = trim($crawler->filter('.pm-content .pm-desc')->html());
        $fields = collect($crawler->filter('#product-other-detail div.row')->each(function ($node) {
            return [Str::slug(trim($node->filter('div.left')->text())) => trim($node->filter('div.right')->text())];
        }))->reject(function ($value) {
            return null == $value;
        })->toArray();

        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                $item->{$key} = empty($value) ? null : $value;
            }
        }

        $fields = collect($crawler->filter('#project div.row')->each(function ($node) {
            return [Str::slug(trim($node->filter('div.left')->text())) => trim($node->filter('div.right')->text())];
        }))->reject(function ($value) {
            return null == $value;
        })->toArray();

        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                $item->{$key} = empty($value) ? null : $value;
            }
        }

        $fields = collect($crawler->filter('#divCustomerInfo div.right-content')->each(function ($node) {
            $key = trim($node->filter('div.left')->text());
            $value = trim($node->filter('div.right')->text());

            if ($key === 'Email') {
                $value = $this->extractEmail($value);
            }
            return [Str::slug($key) => $value];
        }))->reject(function ($value) {
            return null == $value;
        })->toArray();

        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                $item->{$key} = empty($value) ? null : $value;
            }
        }

        return $item;
    }

    /**
     * @param  string  $text
     * @return string
     */
    private function extractEmail(string $text): string
    {
        $regex = '`([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})`';
        preg_match_all($regex, html_entity_decode($text), $matches);

        $matches = array_unique($matches[0]);

        return reset($matches);
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

        $links = $crawler->filter('.search-productItem')->each(function ($node) {
            return [
                'url' => self::CRAWLER_ENDPOINT.$node->filter('h3 a')->attr('href'),
                'title' => $node->filter('h3 a')->attr('title'),
                'cover' => $node->filter('.p-main-image-crop img.product-avatar-img')->attr('src'),
            ];
        });

        return collect($links);
    }

    /**
     * @param  string|null  $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri = null): int
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        $lastPath = explode('/', Url::fromString($crawler->selectLink('>')->attr('href'))->getPath());

        return (int) str_replace('p', '', end($lastPath));
    }

    /**
     * @param  array  $conditions
     * @return Collection|null
     */
    public function search(array $conditions = []): ?Collection
    {
        return $this->getIndexLinks($this->buildUrl('tags/'.implode('/', $conditions)));
    }

    /**
     * @param  Url  $url
     * @param  int  $page
     * @return string
     */
    protected function buildUrlWithPage(Url $url, int $page): string
    {
        return $this->buildUrl($url->getPath().'/p'.$page);
    }
}
