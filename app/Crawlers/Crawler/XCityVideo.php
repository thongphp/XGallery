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
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class XCityVideoModel
 * @package App\Services\Crawler
 */
final class XCityVideo extends AbstractCrawler
{
    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     *
     * @param  string  $itemUri
     *
     * @return \App\Models\Jav\XCityProfile|null
     */
    public function getItem(string $itemUri): ?\App\Models\Jav\XCityProfile
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        $item = app(\App\Models\Jav\XCityProfile::class);
        $item->title = $crawler->filter('#program_detail_title')->text(null, false);
        $item->url = $itemUri;
        $item->cover = $crawler->filter('div.photo a')->attr('href');
        $item->gallery = collect($crawler->filter('img.launch_thumbnail')->each(static function ($el) {
            return $el->attr('src');
        }))->unique()->toArray();

        $item->actresses = collect($crawler->filter('.bodyCol ul li.credit-links a')->each(static function ($el) {
            return trim($el->text());
        }))->unique()->toArray();

        // Get all fields
        $fields = collect($crawler->filter('.bodyCol ul li')->each(
            function ($li) {
                $node = $li->filter('.koumoku');
                if ($node->count() == 0) {
                    return [];
                }

                $label = $node->text();

                switch ($label) {
                    case '★Favorite':
                        return ['favorite' => (int) $li->filter('.favorite-count')->text()];
                    case 'Sales Date':
                        return [
                            'sales_date' => DateTime::createFromFormat(
                                'Y/m/j',
                                trim(str_replace('Sales Date', '', $li->text()))
                            )
                        ];
                    case 'Label/Maker':
                        return [
                            'label' => $li->filter('#program_detail_maker_name')->text(),
                            'marker' => $li->filter('#program_detail_label_name')->text(),
                        ];
                    case 'Series':
                        return ['series' => trim(str_replace('Series', '', $li->text()))];
                    case 'Genres':
                        $genres = $li->filter('a.genre')->each(
                            static function ($a) {
                                return trim($a->text(null, false));
                            }
                        );

                        return ['genres' => $genres];
                    case 'Director':
                        $node = $li->filter('#program_detail_director');
                        return ['director' => $node->count() > 0 ? $node->text() : null];
                    case 'Item Number':
                        return ['item_number' => trim($li->filter('#hinban')->text())];
                    case 'Running Time':
                        return [
                            'time' => (int) trim(str_replace(
                                ['Running Time', 'min', '.'],
                                ['', '', ''],
                                $li->text(null, false)
                            )),
                        ];

                    case 'Release Date':
                        $releaseDate = trim(str_replace('Release Date', '', $li->text(null, false)));
                        if (!empty($releaseDate) && strpos($releaseDate, 'undelivered now') === false) {
                            return ['release_date' => DateTime::createFromFormat('Y/m/j', $releaseDate)];
                        }
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

        $item->description = $crawler->filter('p.lead')->text();

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
     * @param  string  $searchTerm
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
