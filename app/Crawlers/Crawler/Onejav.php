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
 * Class Onejav
 * @package App\Crawlers\Crawler
 */
final class Onejav extends AbstractCrawler
{
    const ENDPOINT = 'https://onejav.com';

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
        /**
         * @TODO Url property should not be there. If itemUri is NULL will cause trouble
         */
        $item->url = trim($itemUri);

        if ($crawler->filter('.columns img.image')->count()) {
            $item->cover = trim($crawler->filter('.columns img.image')->attr('src'));
        }

        if ($crawler->filter('h5 a')->count()) {
            $item->title = (trim($crawler->filter('h5 a')->text(null, false)));
        }

        if ($crawler->filter('h5 span')->count()) {
            $item->size = trim($crawler->filter('h5 span')->text(null, false));

            if (strpos($item->size, 'MB') !== false) {
                $item->size = (float) trim(str_replace('MB', '', $item->size));
                $item->size = $item->size / 1024;
            } elseif (strpos($item->size, 'GB') !== false) {
                $item->size = (float) trim(str_replace('GB', '', $item->size));
            }
        }

        $item->date = $this->convertStringToDateTime(trim($crawler->filter('.subtitle.is-6 a')->attr('href')));
        $item->tags = collect($crawler->filter('.tags .tag')->each(
            function ($tag) {
                return trim($tag->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        });

        $description = $crawler->filter('.level.has-text-grey-dark');
        $item->description = trim($description->count() ? trim($description->text(null, false)) : null);

        $item->actresses = collect($crawler->filter('.panel .panel-block')->each(
            function ($actress) {
                return trim($actress->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        });

        $item->torrent = self::ENDPOINT.trim($crawler->filter('.control.is-expanded a')->attr('href'));

        return $item;
    }

    /**
     * @param  string  $date
     * @return DateTime|null
     */
    private function convertStringToDateTime(string $date): ?DateTime
    {
        try {
            $date = trim($date, '/');
            if (!$dateTime = DateTime::createFromFormat('Y/m/j', $date)) {
                return null;
            }

            return $dateTime;
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

        $links = $crawler->filter('.container .columns')->each(function ($el) {
            $data = [];

            if ($el->filter('.columns img.image')->count()) {
                $data['cover'] = trim($el->filter('.columns img.image')->attr('src'));
            }

            $data['url'] = self::ENDPOINT.trim($el->filter('h5.title a')->attr('href'));

            if ($el->filter('h5 a')->count()) {
                $data['title'] = (trim($el->filter('h5 a')->text(null, false)));
            }

            if ($el->filter('h5 span')->count()) {
                $data['size'] = trim($el->filter('h5 span')->text(null, false));

                if (strpos($data['size'], 'MB') !== false) {
                    $data['size'] = (float) trim(str_replace('MB', '', $data['size']));
                    $data['size'] = $data['size'] / 1024;
                } elseif (strpos($data['size'], 'GB') !== false) {
                    $data['size'] = (float) trim(str_replace('GB', '', $data['size']));
                }
            }

            // Date
            $data['date'] = $this->convertStringToDateTime(trim($el->filter('.subtitle.is-6 a')->attr('href')));
            $data['tags'] = collect($el->filter('.tags .tag')->each(
                function ($tag) {
                    return trim($tag->text(null, false));
                }
            ))->reject(function ($value) {
                return null === $value || empty($value);
            })->toArray();

            $description = $el->filter('.level.has-text-grey-dark');
            $data['description'] = trim($description->count() ? trim($description->text(null, false)) : null);

            $data['actresses'] = collect($el->filter('.panel .panel-block')->each(
                function ($actress) {
                    return trim($actress->text(null, false));
                }
            ))->reject(function ($value) {
                return null === $value || empty($value);
            })->toArray();

            $data['torrent'] = self::ENDPOINT.trim($el->filter('.control.is-expanded a')->attr('href'));

            return $data;
        });

        return collect($links);
    }

    /**
     * @param  string  $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri = null): int
    {
        /**
         * @TODO Actually we can't get last page. Recursive is required
         */
        $crawler = null === $indexUri ? $this->crawler : $this->crawl($indexUri);

        try {
            $page = (int) $crawler->filter('a.pagination-link')->last()->text();
            $class = $crawler->filter('a.pagination-link')->last()->attr('class');

            if (strpos($class, 'is-inverted') !== false) {
                $url = $this->buildUrlWithPage(Url::fromString($indexUri), $page);
                $page = $this->getIndexPagesCount($url);
            }

            return $page;
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
        $url = $this->buildUrl('/search/'.implode('', $conditions));
        return $this->getIndexLinks($url);
    }
}
