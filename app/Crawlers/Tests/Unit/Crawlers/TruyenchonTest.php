<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Truyenchon;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\Truyentranh\TruyenchonModel;
use Illuminate\Contracts\Foundation\Application;

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
        $this->assertInstanceOf(TruyenchonModel::class, $item);
        $this->testModelProperties([
            'url', 'images'
        ], $item);
    }
}
