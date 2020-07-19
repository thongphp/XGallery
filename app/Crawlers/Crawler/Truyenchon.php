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
use App\Crawlers\Middleware\TruyenchonRateLimitStore;
use App\Models\Truyenchon\TruyenchonChapterModel;
use Exception;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Collection;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;
use Spatie\Url\Url;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Truyenchon
 * @package App\Crawlers\Crawler
 */
final class Truyenchon
{
    /**
     * @param  array  $options
     * @return HttpClient
     */
    public function getClient(array $options = []): HttpClient
    {
        $stack = HandlerStack::create();
        $stack->push(RateLimiterMiddleware::perSecond(10, new TruyenchonRateLimitStore()));
        $options['handler'] = $stack;
        $options = array_merge($options, config('httpclient'));

        return app(HttpClient::class, $options);
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
     * @param  string  $chapterUrl
     * @return TruyenchonChapterModel|null
     */
    public function getItem(string $chapterUrl): ?TruyenchonChapterModel
    {
        if (!$crawler = $this->crawl($chapterUrl)) {
            return null;
        }

        $item = new TruyenchonChapterModel;
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
