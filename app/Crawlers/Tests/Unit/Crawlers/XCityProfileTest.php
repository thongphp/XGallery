<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\XCityProfile;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;

class XCityProfileTest extends TestCase
{
    use HasModelTests;

    private XCityProfile $crawler;

    public function testGetItem(): void
    {
        $item = $this->crawler->getItem('https://xxx.xcity.jp/idol/detail/12144/');
        $this->assertInstanceOf(\App\Models\Jav\XCityProfileModel::class, $item);
        $this->testModelProperties($item->getFillable(), $item);
    }

    public function testGetIndexPagesCount(): void
    {
        $this->assertEquals(
            68,
            $this->crawler->getIndexPagesCount('https://xxx.xcity.jp/idol/?kana=%E3%81%8B&num=30')
        );

        $this->assertEquals(
            63,
            $this->crawler->getIndexPagesCount('https://xxx.xcity.jp/idol/?kana=%E3%81%95&num=30')
        );
    }

    public function testSearch(): void
    {
        $results = $this->crawler->search('Maria');
        $this->assertEquals(30, $results->count());
        $item = $results->first();
        $this->assertIsString($item);
        $this->assertNotFalse(filter_var($item, FILTER_VALIDATE_URL));
    }

    public function testGetItemLinks(): void
    {
        $itemLinks = $this->crawler->getItemLinks('https://xxx.xcity.jp/idol/?kana=%E3%81%82&num=30');
        $this->assertEquals(30, $itemLinks->count());
        $item = $itemLinks->first();
        $this->assertIsString($item);
        $this->assertNotFalse(filter_var($item, FILTER_VALIDATE_URL));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->crawler = app(XCityProfile::class);
    }
}
