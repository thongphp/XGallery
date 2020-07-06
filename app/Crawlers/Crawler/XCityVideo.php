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
use App\Models\Jav\XCityVideoModel;
use DateTime;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class XCityVideoModel
 * @package App\Services\Crawler
 */
final class XCityVideo
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
     * @SuppressWarnings("PHPMD.NPathComplexity")
     *
     * @param string $itemUri
     *
     * @return XCityVideoModel|null
     */
    public function getItem(string $itemUri): ?XCityVideoModel
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = app(XCityVideoModel::class);
        $item->title = $crawler->filter('#program_detail_title')->text(null, false);
        $item->url = $itemUri;
        $item->gallery = collect($crawler->filter('img.launch_thumbnail')->each(static function ($el) {
            return $el->attr('src');
        }))->unique()->toArray();

        $item->actresses = collect($crawler->filter('.bodyCol ul li.credit-links a')->each(static function ($el) {
            return ['https://xxx.xcity.jp'.$el->attr('href'), trim($el->text())];
        }))->unique()->toArray();

        // Get all fields
        $fields = collect($crawler->filter('.bodyCol ul li')->each(
            static function ($li) {
                if (strpos($li->text(null, false), '★Favorite') !== false) {
                    return ['favorite' => (int) str_replace('★Favorite', '', $li->text(null, false))];
                }
                if (strpos($li->text(null, false), 'Sales Date') !== false) {
                    return [
                        'sales_date' => DateTime::createFromFormat(
                            'Y/m/j',
                            trim(str_replace('Sales Date', '', $li->text(null, false)))
                        )
                    ];
                }
                if (strpos($li->text(null, false), 'Label/Maker') !== false) {
                    return [
                        'label' => $li->filter('#program_detail_maker_name')->text(),
                        'marker' => $li->filter('#program_detail_label_name')->text(),
                    ];
                }
                if (strpos($li->text(null, false), 'Genres') !== false) {
                    $genres = $li->filter('a.genre')->each(
                        static function ($a) {
                            return trim($a->text(null, false));
                        }
                    );

                    return ['genres' => $genres];
                }
                if (strpos($li->text(null, false), 'Series') !== false) {
                    return ['series' => trim(str_replace('Series', '', $li->text(null, false)))];
                }
                if (strpos($li->text(null, false), 'Director') !== false) {
                    return ['director' => trim(str_replace('Director', '', $li->text(null, false)))];
                }
                if (strpos($li->text(null, false), 'Item Number') !== false) {
                    return ['item_number' => trim(str_replace('Item Number', '', $li->text(null, false)))];
                }
                if (strpos($li->text(null, false), 'Running Time') !== false) {
                    return [
                        'time' => (int) trim(str_replace(
                            ['Running Time', 'min', '.'],
                            ['', '', ''],
                            $li->text(null, false)
                        )),
                    ];
                }
                if (strpos($li->text(null, false), 'Release Date') !== false) {
                    $releaseDate = trim(str_replace('Release Date', '', $li->text(null, false)));
                    if (!empty($releaseDate) && strpos($releaseDate, 'undelivered now') === false) {
                        return ['release_date' => DateTime::createFromFormat('Y/m/j', $releaseDate)];
                    }
                }
                if (strpos($li->text(null, false), 'Description') !== false) {
                    return ['description' => trim(str_replace('Description', '', $li->text(null, false)))];
                }

                return null;
            }
        ))->reject(static function ($value) {
            return null === $value;
        })->toArray();

        foreach ($fields as $field) {
            foreach ($field as $key => $value) {
                if ($key === 'item_number') {
                    $value = implode('-', preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $value));
                }
                $item->{$key} = empty($value) ? null : $value;
            }
        }

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

        $links = $crawler->filter('.x-itemBox')->each(static function ($el) {
            return 'https://xxx.xcity.jp'.$el->filter('.x-itemBox-package a')->attr('href');
        });

        return collect($links);
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

        $nodes = $crawler->filter('ul.pageScrl li.next');

        if ($nodes->count() === 0 || $nodes->previousAll()->filter('li a')->count() === 0) {
            return 1;
        }

        return (int) $crawler->filter('ul.pageScrl li.next')->previousAll()->filter('li a')->text(null, false);
    }

    /**
     * @param string $searchTerm
     *
     * @return Collection|null
     */
    public function search(string $searchTerm): ?Collection
    {
        return $this->getItemLinks(
            'https://xxx.xcity.jp/avod/result/?'.http_build_query([
                'genre' => 'avod', 'q' => $searchTerm, 'sg' => 'avod',
            ])
        );
    }
}
