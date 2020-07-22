<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Kissgoddess;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\KissGoddessModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class KissGoddessTest extends TestCase
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

    public function urlDataProvider(): array
    {
        return [
            [
                'url' => 'https://kissgoddess.com/album/32774.html',
            ],
            [
                'url' => 'https://kissgoddess.com/album/32313.html'
            ]
        ];
    }

    /**
     * @dataProvider urlDataProvider
     * @param  string  $url
     */
    public function testGetItem(string $url): void
    {
        $item = $this->crawler->getItem($url);
        $this->assertInstanceOf(KissGoddessModel::class, $item);
        $this->assertModelProperties(['url','images'], $item);
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
