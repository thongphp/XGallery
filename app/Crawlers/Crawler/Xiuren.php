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
use App\Models\XiurenModel;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Xiuren
 * @package App\Services\Crawler
 */
final class Xiuren
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
     * @param  string  $itemUri
     * @return XiurenModel|null
     */
    public function getItem(string $itemUri): ?XiurenModel
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = new XiurenModel;
        $item->url = $itemUri;
        $item->images = collect($crawler->filter('#main .post .photoThum a')->each(
            function ($a) {
                return $a->attr('href');
            }
        ))->reject(function ($value) {
            return null === $value;
        })->toArray();

        return $item;
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

        return collect($crawler->filter('#main .loop .content a')->each(
            function ($el) {
                return [
                    'url' => $el->attr('href'),
                    'cover' => $el->filter('img')->attr('src'),
                ];
            }
        ));
    }

    /**
     * @param  string|null  $indexUri
     * @return int
     */
    public function getIndexPagesCount(string $indexUri = null): int
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        try {
            $pages = explode('/', $crawler->filter('#page .info')->text());

            return (int) end($pages);
        } catch (Exception $exception) {
            return 1;
        }
    }
}
