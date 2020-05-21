<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Crawlers\Crawler;

use Exception;
use Illuminate\Support\Collection;
use Spatie\Url\Url;
use stdClass;

/**
 * Class Nhaccuatui
 * @package App\Services\Crawler
 */
final class Nhaccuatui extends AbstractCrawler
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

        $text = $crawler->text(null, false);
        $start = strpos($text, '/flash/xml?html5=true&key1=');
        $end = strpos($text, '"', $start);
        $url = substr($text, $start, $end - $start);

        $url = $this->buildUrl($url);

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        if (!$xml = $this->getClient()->request('GET', $url)) {
            return null;
        }

        if (!$simpleXmlEl = simplexml_load_string($xml)) {
            return null;
        }

        try {
            $item = new stdClass;
            $item->url = trim($url);
            $item->title = trim((string) $simpleXmlEl->track->title);
            $item->creator = trim((string) $simpleXmlEl->track->creator);
            $item->download = trim((string) $simpleXmlEl->track->location);

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
        return $this->getItems($indexUri, 'li.list_song');
    }

    /**
     * @param  string  $indexUri
     * @param  string  $filter
     * @return Collection|null
     */
    private function getItems(string $indexUri, string $filter): ?Collection
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return null;
        }

        try {
            return collect($crawler->filter($filter)->each(function ($node) {
                return $this->extractData($node);
            }));
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param $node
     * @return array
     */
    private function extractData($node): array
    {
        try {
            return [
                'url' => $node->filter('a.name_song')->attr('href'),
                'name' => $node->filter('a.name_song')->text(),
                'singers' => $node->filter('a.name_singer')->each(function ($singer) {
                    return trim($singer->text());
                }),
            ];
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @param  string  $indexUri
     * @return Collection|null
     */
    public function getItemsInPlaylist(string $indexUri): ?Collection
    {
        return $this->getItems($indexUri, '.item_content');
    }

    /**
     * Nghe gi hom nay ( playlist ) & Moi phat hanh ( song )
     * @return Collection|null
     */
    public function getDashboard(): ?Collection
    {
        return $this->getItems($this->buildUrl(), '.info_album');
    }

    /**
     * @return Collection|null
     */
    public function getHotTopics(): ?Collection
    {
        return collect($this->crawl($this->buildUrl())->filter('.box_topic_music ul li a')->each(function ($node) {
            return [
                'title' => $node->attr('title'),
                'url' => $node->attr('href')
            ];
        }));
    }

    /**
     * @param  string  $path
     * @return Collection|null
     */
    public function getTop100(string $path): ?Collection
    {
        return $this->getItems($this->buildUrl($path), '.box_info_field');
    }

    /**
     * @param  string  $indexUri
     * @return int|null
     */
    public function getIndexPagesCount(string $indexUri = null): int
    {
        if (!$crawler = $this->crawl($indexUri)) {
            return 1;
        }

        try {
            return (int) Url::fromString($crawler->selectLink('Trang cuối')->attr('href'))->getQueryParameter('page');
        } catch (Exception $exception) {
            return 1;
        }
    }

    /**
     * @param  array  $conditions
     * @return Collection
     */
    public function search(array $conditions = []): ?Collection
    {
        return $this->getIndexLinks($this->buildUrl('/tim-nang-cao', $conditions));
    }
}
