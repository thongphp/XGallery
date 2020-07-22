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

    public function urlDataProvider(): array
    {
        return [
            [
                'url' => 'https://batdongsan.com.vn/ban-can-ho-chung-cu-duong-ven-bien-xa-phuoc-thuan-prj-ho-tram-complex/chi-1-4-ty-quy-khach-hang-so-huu-ngay-comlex-so-ng-so-huu-vinh-vien-pr26133982',
                'expectTitle' => 'Chỉ 1.4 tỷ qúy khách hàng sở hữu ngay căn hộ biển Hồ Tràm Comlex, sổ hồng sở hữu vĩnh viễn',
            ],
            [
                'url' => 'https://batdongsan.com.vn/ban-nha-biet-thu-lien-ke-duong-tan-mai-phuong-hoang-van-thu-4-prj-louis-city-hoang-mai/co-hoi-vang-mua-suat-ngoai-giao-gia-tot-dot-dau-vao-ten-truc-tiep-cdt-du-an-mai-pr24905461',
                'expectTitle' => 'Bán suất ngoại giao Louis City Hoàng Mai - Vào tên trực tiếp CĐT - giá ưu đãi'
            ],
            [
                'url' => 'https://batdongsan.com.vn/ban-dat-duong-bau-sen-phuong-bau-sen/chinh-chu-lo-25-303-mt-kinh-doanh-ngay-ubnd-p-sen-pr26162851',
                'expectTitle' => 'Chính chủ lô 25/303 mt kinh doanh ngay UBND P. Bàu Sen'
            ],
            [
                'url' => 'https://batdongsan.com.vn/ban-nha-rieng-duong-70-2-xa-van-canh-1/hot-co-hoi-co-o-to-do-cua-tai-phuong-ha-noi-pr26120640',
                'expectTitle' => 'Hot cơ hội có nhà ô tô đỗ cửa tại Vân Canh, Phương Canh, Hà Nội'
            ]
        ];
    }

    /**
     * @dataProvider urlDataProvider
     * @param  string  $url
     */
    public function testGetItem(string $url, string $expectTitle): void
    {
        $item = $this->crawler->getItem($url);
        $this->assertInstanceOf(BatdongsanModel::class, $item);
        $this->assertSame($expectTitle, $item->name);
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
        $this->assertGreaterThan(9000, $this->crawler->getIndexPagesCount('https://batdongsan.com.vn/nha-dat-ban'));
    }
}
