<?php

namespace App\Crawlers\Crawler;

use App\Services\Client\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCrawler
{
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
    protected function crawl(string $uri, array $options = []): ?Crawler
    {
        if (!$response = $this->getClient()->request(Request::METHOD_GET, $uri, $options)) {
            return null;
        }

        return new Crawler($response, $uri);
    }
}
