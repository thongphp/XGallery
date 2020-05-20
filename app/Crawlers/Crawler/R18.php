<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Spatie\Url\Url;
use stdClass;

/**
 * Class R18
 * @package App\Services\Crawler
 */
final class R18 extends AbstractCrawler
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

        try {
            $item = new stdClass;
            $item->url = $itemUri;
            $item->cover = trim($crawler->filter('.detail-single-picture img')->attr('src'));
            $item->name = trim($crawler->filter('.product-details-page h1')->text(null, false));
            $item->categories = collect($crawler->filter('.product-categories-list a')->each(
                function ($el) {
                    return trim($el->text(null, false));
                }
            ))->reject(function ($value) {
                return null === $value || empty($value);
            })->toArray();

            $fields = collect($crawler->filter('.product-onload .product-details dt')->each(
                function ($dt) {
                    $text = trim($dt->text(null, false));
                    $value = str_replace(['-'], [''], $dt->nextAll()->text(null, false));

                    return [strtolower(str_replace(' ', '_', str_replace([':'], [''], $text))) => trim($value)];
                }
            ))->reject(function ($value) {
                return null === $value || empty($value);
            })->toArray();

            foreach ($fields as $field) {
                foreach ($field as $key => $value) {
                    $item->{$key} = empty($value) ? null : $value;
                }
            }

            if (isset($item->release_date)) {
                try {
                    $date = trim($item->release_date, '/');
                    if (!$dateTime = DateTime::createFromFormat('M. d, Y', $date)) {
                        if (!$dateTime = DateTime::createFromFormat('M d, Y', $date)) {
                            $dateTime = null;
                        }
                    }

                    $item->release_date = $dateTime;
                } catch (Exception $exception) {
                    $item->release_date = null;
                }
            }

            $item->actress = collect($crawler->filter('.product-actress-list a span')->each(
                function ($span) {
                    return trim($span->text(null, false));
                }
            ))->reject(function ($value) {
                return null === $value || empty($value);
            })->toArray();

            if ($crawler->filter('a.js-view-sample')->count()) {
                $item->sample = $crawler->filter('a.js-view-sample')->attr('data-video-high');
            }

            $item->gallery = collect($crawler->filter('.product-gallery a img.lazy')->each(function ($img) {
                return $img->attr('data-original');
            }))->toArray();

            return $item;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param  string|null  $indexUri
     * @return Collection
     */
    public function getItemLinks(string $indexUri = null): ?Collection
    {
        $crawler = null === $indexUri ? $this->crawler : $this->crawl($indexUri);

        if (!$crawler) {
            return null;
        }

        $links = $crawler->filter('.main .cmn-list-product01 li.item-list a')->each(
            function ($el) {
                if ($el->attr('href') === null) {
                    return false;
                }

                $data = [];

                $url = explode('?', $el->attr('href'));
                $data['url'] = $url[0];

                if ($el->filter('img.lazy')->count()) {
                    $data['cover'] = $el->filter('img.lazy')->attr('data-original');
                }

                return $data;
            }
        );

        return collect($links)->reject(function ($value) {
            return false === $value;
        });
    }

    /**
     * @param  string  $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri = null): int
    {
        $crawler = null === $indexUri ? $this->crawler : $this->crawl($indexUri);

        if (!$crawler) {
            return 1;
        }

        try {
            return (int) $crawler->filter('li.next')->previousAll()->filter('a')->text();
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
        return $this->getIndexLinks($this->buildUrl('common/search/', $conditions));
    }

    /**
     * @param  Url  $url
     * @param  int  $page
     * @return string
     */
    protected function buildUrlWithPage(Url $url, int $page): string
    {
        return $this->buildUrl(
            $url->getPath().'/page='.$page,
            $url->getAllQueryParameters(),
            false
        );
    }
}
