<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use App\Models\Jav\XCityProfileModel;
use App\Services\Client\HttpClient;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class XCityProfileModel
 * @package App\Crawlers\Crawler
 */
final class XCityProfile
{
    protected array $months = [
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

    /**
     * @return HttpClient
     */
    public function getClient(): HttpClient
    {
        return new HttpClient();
    }

    /**
     * @param  string  $uri
     * @param  array  $options
     * @return Crawler
     */
    public function crawl(string $uri, array $options = []): ?Crawler
    {
        if (!$response = $this->getClient()->request(Request::METHOD_GET, $uri, $options)) {
            return null;
        }

        return new Crawler($response, $uri);
    }

    /**
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     *
     * @param  string  $itemUri
     *
     * @return XCityProfileModel|null
     */
    public function getItem(string $itemUri): ?XCityProfileModel
    {
        if (!$crawler = $this->crawl($itemUri)) {
            return null;
        }

        try {
            $item = app(XCityProfileModel::class);
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

                        if (!isset($this->months[$days[1]])) {
                            return null;
                        }

                        $month = $this->months[$days[1]];
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

                    return null;
                }
            ))->reject(static function ($value) {
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
        if (!$crawler = $this->crawl($indexUri)) {
            return null;
        }

        if ($crawler->filter('.itemBox p.tn')->count() !== 0) {
            $links = $crawler->filter('.itemBox p.tn')->each(static function ($el) {
                return 'https://xxx.xcity.jp/idol/'.$el->filter('a')->attr('href');
            });

            return collect($links);
        }

        return collect($crawler->filter('.itemBox p.name a')->each(static function ($el) {
            return 'https://xxx.xcity.jp/idol/'.$el->filter('a')->attr('href');
        }));
    }

    /**
     * @param  string  $indexUri
     * @return int
     */
    public function getIndexPagesCount(string $indexUri): int
    {
        /**
         * @TODO Actually we can't get last page. Recursive is required
         */
        if (!$crawler = $this->crawl($indexUri)) {
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
     * @param  string  $name
     * @return Collection|null
     */
    public function search(string $name): ?Collection
    {
        return $this->getItemLinks(
            'https://xxx.xcity.jp/idol/?'.http_build_query(['genre' => 'idol', 'q' => $name, 'sg' => 'idol'])
        );
    }
}
