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
 * Class XCityProfile
 * @package App\Services\Crawler
 */
final class XCityProfile extends AbstractCrawler
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
            $item->name = $crawler->filter('.itemBox h1')->text(null, false);
            $item->url = $itemUri;
            $item->cover = $crawler->filter('.photo p.tn img')->attr('src');
            $fields = collect($crawler->filter('#avidolDetails dl.profile dd')->each(
                function ($dd) {
                    $text = $dd->text(null, false);
                    if (strpos($text, '★Favorite') !== false) {
                        return ['favorite' => (int) str_replace('★Favorite', '', $text)];
                    }
                    if (strpos($text, 'Date of birth') !== false) {
                        $birthday = trim(str_replace('Date of birth', '', $text));
                        if (empty($birthday)) {
                            return null;
                        }
                        $days = explode(' ', $birthday);
                        $month = $this->getMonth($days[1]);
                        if (!$month) {
                            return null;
                        }

                        return ['birthday' => DateTime::createFromFormat('Y-m-d', $days[0].'-'.$month.'-'.$days[2])];
                    }
                    if (strpos($text, 'Blood Type') !== false) {
                        $bloodType = str_replace(['Blood Type', 'Type', '-', '_'], ['', '', '', ''], $text);

                        return ['blood_type' => trim($bloodType)];
                    }
                    if (strpos($text, 'City of Born') !== false) {
                        return ['city' => trim(str_replace('City of Born', '', $text))];
                    }
                    if (strpos($text, 'Height') !== false) {
                        return ['height' => trim(str_replace('cm', '', str_replace('Height', '', $text)))];
                    }
                    if (strpos($text, 'Size') !== false) {
                        $sizes = trim(str_replace('Size', '', $text));
                        if (empty($sizes)) {
                            return null;
                        }
                        $sizes = explode(' ', $sizes);
                        foreach ($sizes as $index => $size) {
                            switch ($index) {
                                case 0:
                                    $size = str_replace('B', '', $size);
                                    $size = explode('(', $size);
                                    $breast = empty(trim($size[0])) ? null : (int) $size[0];
                                    break;
                                case 1:
                                    $size = str_replace('W', '', $size);
                                    $size = explode('(', $size);
                                    $waist = empty(trim($size[0])) ? null : (int) $size[0];
                                    break;
                                case 2:
                                    $size = str_replace('H', '', $size);
                                    $size = explode('(', $size);
                                    $hips = empty(trim($size[0])) ? null : (int) $size[0];
                                    break;
                            }
                        }

                        return [
                            'breast' => $breast ?? null,
                            'waist' => $waist ?? null,
                            'hips' => $hips ?? null,
                        ];
                    }
                }
            ))->reject(function ($value) {
                return null == $value;
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
     * @TODO Remove this function
     * @param  string  $index
     * @return bool|mixed
     */
    private function getMonth($index)
    {
        $months = [
            'Jan' => '01',
            'Feb' => '02',
            'Mar' => '03',
            'Apr' => '04',
            'May' => '05',
            'Jun' => '06',
            'Jul' => '07',
            'Aug' => '08',
            'Sep' => '09',
            'Oct' => '10',
            'Nov' => '11',
            'Dec' => '12',
        ];
        if (isset($months[$index])) {
            return $months[$index];
        }

        return false;
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

        if ($crawler->filter('.itemBox p.tn')->count() !== 0) {
            $links = $crawler->filter('.itemBox p.tn')->each(function ($el) {
                return [
                    'url' => 'https://xxx.xcity.jp/idol/'.$el->filter('a')->attr('href'),
                    'name' => $el->filter('a')->attr('title'),
                    'cover' => $el->filter('a img')->attr('src')
                ];
            });

            return collect($links);
        }

        $links = $crawler->filter('.itemBox p.name a')->each(function ($el) {
            return [
                'url' => 'https://xxx.xcity.jp/idol/'.$el->filter('a')->attr('href'),
                'name' => $el->filter('a')->attr('title'),
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
        return $this->getIndexLinks($this->buildUrl('idol/', $conditions));
    }
}
