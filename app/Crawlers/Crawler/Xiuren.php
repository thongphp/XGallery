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
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Xiuren
 * @package App\Crawlers\Crawler
 */
final class Xiuren extends AbstractCrawler
{
    /**
     * @param string $itemUri
     *
     * @return \App\Models\Xiuren|null
     */
    public function getItem(string $itemUri): ?\App\Models\Xiuren
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = app(\App\Models\Xiuren::class);
        $item->url = $itemUri;
        $item->images = collect(
            $crawler->filter('#main .post .photoThum a')->each(
                static function ($a) {
                    return $a->attr('href');
                }
            )
        )->reject(
            static function ($value) {
                return null === $value;
            }
        )->toArray();

        return $item;
    }

    /**
     * @param string|null $indexUri
     *
     * @return Collection
     */
    public function getItemLinks(string $indexUri = null): ?Collection
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return null;
        }

        return collect(
            $crawler->filter('#main .loop .content a')->each(
                function ($el) {
                    return [
                        'url' => $el->attr('href'),
                        'cover' => $el->filter('img')->attr('src'),
                    ];
                }
            )
        );
    }

    /**
     * @param string|null $indexUri
     *
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
