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

    public function testGetItem(): void
    {
        $item = $this->crawler->getItem('https://xxx.xcity.jp/avod/detail/?id=145340');
        $this->assertInstanceOf(XCityVideoModel::class, $item);
        $this->assertModelProperties($item->getFillable(), $item);
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
