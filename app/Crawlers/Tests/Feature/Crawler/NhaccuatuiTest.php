<?php

namespace App\Crawlers\Tests\Feature\Crawler;

use App\Crawlers\Crawler\Nhaccuatui;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasCrawler;

/**
 * Class NhaccuatuiTest
 * @package Tests\Feature\Crawler
 */
class NhaccuatuiTest extends TestCase
{
    use HasCrawler;

    protected array $itemDetails = [
        'https://www.nhaccuatui.com/bai-hat/biet-tim-dau-tuan-hung.51hz7Qf8e8.html' => [
            'url',
            'title',
            'creator',
            'download'
        ]
    ];

    protected array $itemLinks = [
        'https://www.nhaccuatui.com/tim-nang-cao?title=&user=&singer=Tu%E1%BA%A4n+H%C6%B0ng&kbit=&type=1&sort=&direction=2&page=1' => [
            'url',
            'name',
            'singers'
        ]
    ];

    protected array $indexLinks = [
        'https://www.nhaccuatui.com/tim-nang-cao?title=&user=&singer=Tu%E1%BA%A4n+H%C6%B0ng&kbit=&type=1&sort=&direction=2&page=1' => [
            'pageCount' => 25,
            'itemPerPage' => 20
        ]
    ];

    protected string $crawlerClass = Nhaccuatui::class;

    public function testGetItems()
    {
        $items = $this->crawler->getItemsInPlaylist('https://www.nhaccuatui.com/playlist/beautiful-girl-remix-va.oRXDi0WrydLc.html');
        $this->assertIsObject($items, 'Is not collection');
        $this->assertGreaterThan(1, $items->count(), 'Collect count is not larger than 1');
    }

    public function testGetDashboard()
    {
        $items = $this->crawler->getDashboard();
        $this->assertIsObject($items, 'Is not collection');
        $this->assertGreaterThan(1, $items->count(), 'Collect count is not larger than 1');
    }

    public function testGetTop100()
    {
        $items = $this->crawler->getTop100('top100/top-100-nhac-tre.m3liaiy6vVsF.html');
        $this->assertIsObject($items, 'Is not collection');
        $this->assertGreaterThan(1, $items->count(), 'Collect count is not larger than 1');
    }
}
