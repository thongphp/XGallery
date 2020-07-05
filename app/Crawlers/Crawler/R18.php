<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use App\Crawlers\HttpClient;
use App\Models\Jav\R18Model;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class R18
 * @package App\Services\Crawler
 */
final class R18
{
    /**
     * @param  array  $options
     * @return HttpClient
     */
    public function getClient(array $options = []): HttpClient
    {
        return new HttpClient(array_merge($options, config('httpclient')));
    }

    /**
     * @param  string  $uri
     * @param  array  $options
     * @return Crawler
     */
    public function crawl(string $uri, array $options = []): ?Crawler
    {
        if (!$response = $this->getClient($options)->request(Request::METHOD_GET, $uri)) {
            return null;
        }

        return new Crawler($response, $uri);
    }

    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     *
     * @param  string  $itemUri
     * @return R18Model|null
     */
    public function getItem(string $itemUri): ?R18Model
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        try {
            $item = new R18Model;
            $item->url = $itemUri;
            $item->cover = trim($crawler->filter('.detail-single-picture img')->attr('src'));
            $item->title = trim($crawler->filter('.product-details-page h1')->text(null, false));
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

            $item->actresses = collect($crawler->filter('.product-actress-list a span')->each(
                function ($span) {
                    return trim($span->text(null, false));
                }
            ))->reject(function ($value) {
                return null === $value || empty($value);
            })->toArray();

            if ($crawler->filter('a.js-view-sample')->count()) {
                $item->sample = $crawler->filter('a.js-view-sample')->attr('data-video-high');
            }

            $item->galleries = collect($crawler->filter('.product-gallery a img.lazy')->each(function ($img) {
                return $img->attr('data-original');
            }))->toArray();

            if (isset($item->runtime) && !is_int($item->runtime)) {
                $item->runtime = (int) $item->runtime;
            }

            return $item;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param  string  $indexUri
     * @return Collection
     */
    public function getItemLinks(string $indexUri): ?Collection
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return null;
        }

        return collect($crawler->filter('.main .cmn-list-product01 li.item-list a')->each(
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
        ))->reject(function ($value) {
            return false === $value;
        });
    }

    /**
     * @param  string  $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri): int
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        try {
            return (int) $crawler->filter('li.next')->previousAll()->filter('a')->text();
        } catch (Exception $exception) {
            return 1;
        }
    }
}
