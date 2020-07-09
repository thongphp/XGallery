<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Onejav;
use App\Crawlers\Tests\TestCase;
use App\Crawlers\Tests\Traits\HasModelTests;
use App\Models\Jav\OnejavModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * Class OnejavTest
 * @package App\Crawlers\Tests\Unit\Crawlers
 */
class OnejavTest extends TestCase
{
    use HasModelTests;

    /**
     * @var Onejav|Application|mixed
     */
    private Onejav $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(Onejav::class);
    }

    public function testGetDaily(): void
    {
        $items = $this->crawler->getDaily();
        $this->assertInstanceOf(Collection::class, $items);
        $item = $items->first();
        $this->assertInstanceOf(OnejavModel::class, $item);
        $this->assertModelProperties($item->getFillable(), $item);
        $this->assertIsFloat($item->size);
    }

    public function testGetItems(): void
    {
        $items = $this->crawler->getItems('https://onejav.com/2020/07/05');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(10, $items->count());
        $item = $items->first();
        $this->assertInstanceOf(OnejavModel::class, $item);
        $this->assertModelProperties($item->getFillable(), $item);
        $this->assertIsFloat($item->size);
    }

    public function testGetPagesCount(): void
    {
        $this->assertEquals(3, $this->crawler->getIndexPagesCount('https://onejav.com/2020/07/05'));
    }
}
