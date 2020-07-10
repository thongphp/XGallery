<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\XCityVideo;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\Jav\XCityVideoModel;

class XCityVideoTest extends TestCase
{
    use HasModelTests;

    private XCityVideo $crawler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->crawler = app(XCityVideo::class);
    }

    public function itemProviders()
    {
        return [
            [
                'url' => 'https://xxx.xcity.jp/avod/detail/?maker=firststar&id=146516',
                'expectFields' => [
                    'title', 'url', 'cover', 'gallery', 'actresses', 'favorite', 'sales_date', 'label', 'marker',
                    'series',
                    'genres', 'director', 'item_number', 'time', 'release_date', 'description'
                ]
            ],
            [
                'url' => 'https://xxx.xcity.jp/avod/detail/?maker=prestige&id=148334',
                'expectFields' => [
                    'title', 'url', 'cover', 'gallery', 'actresses', 'favorite', 'sales_date', 'label', 'marker',
                    'series',
                    'genres', 'director', 'item_number', 'time', 'release_date', 'description'
                ]
            ],
            [
                'url' => 'https://xxx.xcity.jp/avod/detail/?maker=shin-toho&id=142760',
                'expectFields' => [
                    'title', 'url', 'cover', 'gallery', 'actresses', 'favorite', 'label', 'marker',
                    'series',
                    'genres', 'director', 'item_number', 'time', 'release_date', 'description'
                ]
            ],
            [
                'url' => 'https://xxx.xcity.jp/avod/detail/?maker=kuki&id=6355',
                'expectFields' => [
                    'title', 'url', 'cover', 'gallery', 'actresses', 'favorite', 'sales_date', 'label', 'marker',
                    'series',
                    'genres', 'director', 'item_number', 'time', 'release_date', 'description'
                ]
            ]
        ];
    }

    /**
     * @dataProvider itemProviders
     * @param  string  $url
     * @param  array  $expectFields
     */
    public function testGetItem(string $url, array $expectFields): void
    {
        $item = $this->crawler->getItem($url);
        $this->assertInstanceOf(XCityVideoModel::class, $item);
        $this->assertModelProperties($expectFields, $item);
    }

    public function testGetIndexPagesCount(): void
    {
        $this->assertEquals(
            17,
            $this->crawler->getIndexPagesCount('https://xxx.xcity.jp/avod/list/?style=simple&from_date=20200501&to_date=20200531')
        );

        $this->assertEquals(
            18,
            $this->crawler->getIndexPagesCount('https://xxx.xcity.jp/avod/list/?style=simple&from_date=20200301&to_date=20200331')
        );
    }

    public function testSearch(): void
    {
        $items = $this->crawler->search('Maria');
        $this->assertEquals(30, $items->count());
        $item = $items->first();
        $this->assertIsString($item);
        $this->assertNotFalse(filter_var($item, FILTER_VALIDATE_URL));
    }

    public function testGetItemLinks(): void
    {
        $itemLinks = $this->crawler->getItemLinks('https://xxx.xcity.jp/avod/list/?style=simple');
        $this->assertEquals(30, $itemLinks->count());
        $item = $itemLinks->first();
        $this->assertIsString($item);
        $this->assertNotFalse(filter_var($item, FILTER_VALIDATE_URL));
    }
}
