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
use App\Models\KissGoddessModel;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Kissgoddess
 * @package App\Crawlers\Crawler
 */
final class Kissgoddess
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
     *
     * @return KissGoddessModel|null
     */
    public function getItem(string $itemUri): ?KissGoddessModel
    {
        $pages = $this->getIndexPagesCount($itemUri);
        $item = new KissGoddessModel;
        $item->url = $itemUri;
        $images = collect([]);

        $itemUri = str_replace('.html', '', $itemUri);

        // @todo Too much sub-requests. Reduce it later

        for ($page = 1; $page <= $pages; $page++) {
            $crawler = $this->crawl($itemUri.'_'.$page.'.html');

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
     * @param  string|null  $indexUri
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
                    'url' => 'https://kissgoddess.com'.$el->attr('href'),
                    'title' => $el->attr('title'),
                    'cover' => $el->filter('img')->attr('src'),
                ];
            }
        ));
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
            $count = $crawler->filter('#pages a')->count();
            $pages = $crawler->filter('#pages a');
            $page = $pages->getNode($count - 2);
            $page = explode('/', $page->getAttribute('href'));
            $page = explode('_', end($page));
            $page = end($page);

            return (int) str_replace('.html', '', $page);
        } catch (Exception $exception) {
            return 1;
        }
    }

    public function download(KissGoddessModel $item)
    {
        foreach ($item->images as $image) {
            $this->getClient()->download($image, 'kissgoddess' . DIRECTORY_SEPARATOR . $item->title);
        }
    }
}
