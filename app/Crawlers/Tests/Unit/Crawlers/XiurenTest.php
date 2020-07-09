<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Xiuren;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\XiurenModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class XiurenTest extends TestCase
{
    use HasModelTests;

    /**
     * @var Xiuren|Application|mixed
     */
    private Xiuren $crawler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(Xiuren::class);
    }

    public function testGetItem(): void
    {
        $item = $this->crawler->getItem('http://www.xiuren.org/XiuRen-N01932.html');
        $this->assertInstanceOf(XiurenModel::class, $item);
        $this->assertModelProperties(['url', 'images'], $item);
    }

    public function testGetItems(): void
    {
        $items = $this->crawler->getItemLinks('http://www.xiuren.org/');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(15, $items->count());
    }

    public function testGetPagesCount(): void
    {
        $this->assertEquals(358, $this->crawler->getIndexPagesCount('http://www.xiuren.org/'));
    }
}
