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
use stdClass;

/**
 * Class XCityVideo
 * @package App\Services\Crawler
 */
final class XCityVideo extends AbstractCrawler
{

    /**
     * @param  string  $itemUri
     * @return object|null
     */
    public function getItemDetail(string $itemUri): ?object
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        try {
            $item = new stdClass();
            $item->title = $crawler->filter('#program_detail_title')->text(null, false);
            $item->url = $itemUri;
            $item->gallery = $crawler->filter('img.launch_thumbnail')->each(function ($el) {
                return $el->attr('src');
            });

            $item->actresses = $crawler->filter('.bodyCol ul li.credit-links a')->each(function ($el) {
                return ['https://xxx.xcity.jp'.$el->attr('href'), trim($el->text())];
            });

            // Get all fields
            $fields = collect($crawler->filter('.bodyCol ul li')->each(
                function ($li) {
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
                            function ($a) {
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
                }
            ))->reject(function ($value) {
                return null === $value;
            })->toArray();

            foreach ($fields as $field) {
                foreach ($field as $key => $value) {
                    $item->{$key} = empty($value) ? null : $value;
                }
            }

            return $item;
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

        $links = $crawler->filter('.x-itemBox')->each(function ($el) {
            return [
                'url' => 'https://xxx.xcity.jp'.$el->filter('.x-itemBox-package a')->attr('href'),
                'title' => $el->filter('.x-itemBox-title a')->attr('title'),
                'cover' => $el->filter('.x-itemBox-package img')->attr('src')
            ];
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

        if (!$crawler) {
            return 1;
        }

        $nodes = $crawler->filter('ul.pageScrl li.next');

        if ($nodes->count() === 0 || $nodes->previousAll()->filter('li a')->count() === 0) {
            return 1;
        }

        return (int) $crawler->filter('ul.pageScrl li.next')->previousAll()->filter('li a')->text(null, false);
    }

    /**
     * @param  array  $conditions
     * @return Collection|null
     */
    public function search(array $conditions = []): ?Collection
    {
        return null;
    }
}
