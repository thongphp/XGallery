<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\R18;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\Jav\R18Model;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class R18Test
 * @package App\Crawlers\Tests\Unit\Crawlers
 */
class R18Test extends TestCase
{
    use HasModelTests;

    /**
     * @var R18|Application|mixed
     */
    private R18 $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(R18::class);
    }

    public function testGetItem()
    {
        $item = $this->crawler->getItem('https://www.r18.com/videos/vod/movies/detail/-/id=dnjr00032');
        $this->assertInstanceOf(R18Model::class, $item);
        $this->testModelProperties([
            'url', 'cover', 'title', 'categories', 'release_date', 'runtime', 'director', 'studio', 'label', 'channel',
            'content_id', 'dvd_id', 'series', 'languages', 'actresses', 'sample', 'gallery'
        ], $item);
    }

    public function testGetItemLinks()
    {
        $items = $this->crawler->getItemLinks('https://www.r18.com/videos/vod/anime/list/pagesize=30/price=all/sort=new/type=all/page=1/');
        $this->assertEquals(30, $items->count());
        $item = $items->first();
        $this->assertIsString($item);
        $this->assertTrue(filter_var($item, FILTER_VALIDATE_URL) !== false);
    }

    public function testGetPagesCount()
    {
        $this->assertEquals(
            20,
            $this->crawler->getIndexPagesCount('https://www.r18.com/videos/vod/anime/list/pagesize=30/price=all/sort=new/type=all/page=1/')
        );

        $this->assertEquals(
            1667,
            $this->crawler->getIndexPagesCount('https://www.r18.com/videos/vod/movies/list/pagesize=30/price=all/sort=new/type=all/page=1')
        );
    }
}
