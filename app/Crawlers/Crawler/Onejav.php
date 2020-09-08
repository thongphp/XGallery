<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use App\Services\Client\HttpClient;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Spatie\Url\Url;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Onejav
 * @package App\Crawlers\Crawler
 */
final class Onejav extends AbstractCrawler
{
    public const ENDPOINT = 'https://onejav.com';

    /**
     * @return Collection
     */
    public function getDaily(): Collection
    {
        $indexUrl = self::ENDPOINT . '/' . date('Y/m/d');
        $pages = $this->getIndexPagesCount($indexUrl);

        $items = collect([]);

        for ($page = 1; $page <= $pages; $page++) {
            $items = $items->merge($this->getItems($indexUrl . '?page' . $page));
        }

        return $items;
    }

    /**
     * @param string $indexUri
     * @return Collection
     */
    public function getItems(string $indexUri): Collection
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return collect([]);
        }

        return collect($crawler->filter('.container .columns')->each(function ($el) {
            return $this->parse($el);
        }));
    }

    /**
     * @param string $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri): int
    {
        // Actually we can't get last page. Recursive is required
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        try {
            $page = (int)$crawler->filter('a.pagination-link')->last()->text();
            $class = $crawler->filter('a.pagination-link')->last()->attr('class');

            $url = Url::fromString($indexUri);

            if (strpos($class, 'is-inverted') !== false) {
                $url = $url->getScheme() . '://' . $url->getHost() . $url->getPath() . '?page=' . $page;
                $page = $this->getIndexPagesCount($url);
            }

            return $page;
        } catch (Exception $exception) {
            return 1;
        }
    }

    /**
     * @param string $date
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
     * @param Crawler $crawler
     * @return \App\Models\Jav\Onejav
     */
    private function parse(Crawler $crawler): \App\Models\Jav\Onejav
    {
        $item = app(\App\Models\Jav\Onejav::class);
        $item->url = self::ENDPOINT . trim($crawler->filter('h5.title a')->attr('href'));

        if ($crawler->filter('.columns img.image')->count()) {
            $item->cover = trim($crawler->filter('.columns img.image')->attr('src'));
        }

        if ($crawler->filter('h5 a')->count()) {
            $item->title = (trim($crawler->filter('h5 a')->text(null, false)));
            $item->title = implode('-', preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $item->title));
        }

        if ($crawler->filter('h5 span')->count()) {
            $item->size = trim($crawler->filter('h5 span')->text(null, false));

            if (strpos($item->size, 'MB') !== false) {
                $item->size = (float)trim(str_replace('MB', '', $item->size));
                $item->size = $item->size / 1024;
            } elseif (strpos($item->size, 'GB') !== false) {
                $item->size = (float)trim(str_replace('GB', '', $item->size));
            }
        }

        $item->date = $this->convertStringToDateTime(trim($crawler->filter('.subtitle.is-6 a')->attr('href')));
        $item->tags = collect($crawler->filter('.tags .tag')->each(
            function ($tag) {
                return trim($tag->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->unique()->toArray();

        $description = $crawler->filter('.level.has-text-grey-dark');
        $item->description = $description->count() ? trim($description->text(null, false)) : null;

        $item->actresses = collect($crawler->filter('.panel .panel-block')->each(
            function ($actress) {
                return trim($actress->text(null, false));
            }
        ))->reject(function ($value) {
            return null === $value || empty($value);
        })->unique()->toArray();

        $item->torrent = self::ENDPOINT . trim($crawler->filter('.control.is-expanded a')->attr('href'));

        return $item;
    }
}
