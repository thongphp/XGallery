<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use App\Crawlers\Middleware\TruyenchonRateLimitStore;
use App\Models\Truyenchon\TruyenchonChapter;
use App\Services\Client\HttpClient;
use Exception;
use Illuminate\Support\Collection;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;
use Spatie\Url\Url;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Truyenchon
 * @package App\Crawlers\Crawler
 */
final class Truyenchon extends AbstractCrawler
{
    /**
     * @param  string  $chapterUrl
     * @return TruyenchonChapter|null
     */
    public function getItem(string $chapterUrl): ?TruyenchonChapter
    {
        if (!$crawler = $this->crawl($chapterUrl)) {
            return null;
        }

        $item = app(TruyenchonChapter::class);
        $item->chapterUrl = $chapterUrl;
        $item->images = collect($crawler->filter('.page-chapter img')->each(function ($img) {
            return $img->attr('data-original');
        }))->toArray();

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
     * @param  string  $storyUrl
     * @return Collection
     */
    public function getChapters(string $storyUrl): ?Collection
    {
        if (!$crawler = $this->crawl($storyUrl)) {
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
    public function getStories(string $indexUri = null): ?Collection
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return null;
        }

        return collect($crawler->filter('.ModuleContent .items .item')->each(function ($el) {
            return [
                'url' => $el->filter('.image a')->attr('href'),
                'cover' => $el->filter('img')->attr('data-original'),
                'title' => $el->filter('h3 a')->text(),
            ];
        }));
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
            $pages = $crawler->filter('.pagination li a')->last()->attr('href');

            return (int) Url::fromString($pages)->getQueryParameter('page');
        } catch (Exception $exception) {
            return 1;
        }
    }
}
