<?php

namespace App\Crawlers\Tests\Unit\Crawlers;

use App\Crawlers\Crawler\Onejav;
use App\Crawlers\Tests\TestCase;
use App\Models\Jav\OnejavModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;

/**
 * Class OnejavTest
 * @package App\Crawlers\Tests\Unit\Crawlers
 */
class OnejavTest extends TestCase
{
    /**
     * @var Onejav|Application|mixed
     */
    private Onejav $crawler;

    public function setUp(): void
    {
        parent::setUp();
        $this->crawler = app(Onejav::class);
    }

    public function testGetDaily()
    {
        $items = $this->crawler->getDaily();
        $this->assertInstanceOf(Collection::class, $items);
        $item = $items->first();
        $this->assertInstanceOf(OnejavModel::class, $item);
        // @todo Check all properties are exists
        $this->assertIsFloat($item->size);
    }

    public function testGetItems()
    {
        $items = $this->crawler->getItems('https://onejav.com/2020/07/05');
        $this->assertInstanceOf(Collection::class, $items);
        $this->assertEquals(10, $items->count());
        $item = $items->first();
        $this->assertInstanceOf(OnejavModel::class, $item);
        // @todo Check all properties are exists
        $this->assertIsFloat($item->size);
    }

    public function testGetPagesCount()
    {
        $this->assertEquals(3, $this->crawler->getIndexPagesCount('https://onejav.com/2020/07/05'));
    }
}
