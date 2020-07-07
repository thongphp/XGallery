<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Kissgoddess;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\KissgoddessModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class KissgoddessTest extends TestCase
{
    use HasModelTests;

    /**
     * @var Kissgoddess|Application|mixed
     */
    private Kissgoddess $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(Kissgoddess::class);
    }

    public function testGetItem(): void
    {
        $item = $this->crawler->getItem('https://kissgoddess.com/album/32774.html');
        $this->assertInstanceOf(KissgoddessModel::class, $item);
        $this->testModelProperties($item->getFillable(), $item);
    }

    public function testGetItems(): void
    {
        $items = $this->crawler->getItemLinks('https://kissgoddess.com/gallery/');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(30, $items->count());
    }

    public function testGetPagesCount(): void
    {
        $this->assertEquals(10, $this->crawler->getIndexPagesCount('https://kissgoddess.com/gallery/'));
    }
}
