<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Batdongsan;
use App\Crawlers\Tests\TestCase;
use App\Models\BatdongsanModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

class BatdongsanTest extends TestCase
{
    /**
     * @var Batdongsan|Application|mixed
     */
    private Batdongsan $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(Batdongsan::class);
    }

    public function testGetItem(): void
    {
        $items = $this->crawler->getItem('https://batdongsan.com.vn/ban-can-ho-chung-cu-duong-ven-bien-xa-phuoc-thuan-prj-ho-tram-complex/chi-1-4-ty-quy-khach-hang-so-huu-ngay-comlex-so-ng-so-huu-vinh-vien-pr26133982');
        $this->assertInstanceOf(BatdongsanModel::class, $items);
    }

    public function testGetItemLinks(): void
    {
        $items = $this->crawler->getItemLinks('https://batdongsan.com.vn/nha-dat-ban');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(20, $items->count());
        $item = $items->first();
        $this->assertIsString($item);
        $this->assertNotFalse(filter_var($item, FILTER_VALIDATE_URL));
    }

    public function testGetPagesCount(): void
    {
        $this->assertEquals(9824, $this->crawler->getIndexPagesCount('https://batdongsan.com.vn/nha-dat-ban'));
    }
}
