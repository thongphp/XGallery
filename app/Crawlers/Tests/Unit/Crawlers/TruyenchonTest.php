<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Truyenchon;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\Truyenchon\TruyenchonChapter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class TruyenchonTest extends TestCase
{
    use HasModelTests;

    /**
     * @var Truyenchon|Application|mixed
     */
    private Truyenchon $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(Truyenchon::class);
    }

    public function testGetItem(): void
    {
        $item = $this->crawler->getItem('http://truyenchon.com/truyen/kinh-di-khong-loi/chap-211/599759');
        $this->assertInstanceOf(TruyenchonChapter::class, $item);
        $this->assertModelProperties(['chapterUrl', 'images', 'title'], $item);
    }

    public function testGetChapters()
    {
        $items = $this->crawler->getChapters('http://truyenchon.com/truyen/maohritsu-chu-boss-yousei-academia-20016');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(6, $items->count());
    }

    public function testGetStories()
    {
        $items = $this->crawler->getStories('http://truyenchon.com');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(48, $items->count());

        $item = $items->first();
        $this->assertArrayHasKey('url', $item);
        $this->assertArrayHasKey('cover', $item);
        $this->assertArrayHasKey('title', $item);
    }

    public function testGetPagesCount(): void
    {
        $this->assertGreaterThan(380, $this->crawler->getIndexPagesCount('http://truyenchon.com'));
    }
}
