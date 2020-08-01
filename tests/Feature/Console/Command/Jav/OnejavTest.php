<?php

namespace Tests\Feature\Console\Command\Jav;

use App\Models\CrawlerEndpoints;
use App\Models\Jav\JavMovie;
use App\Models\Jav\Onejav;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnejavTest extends TestCase
{
    use RefreshDatabase;

    public function testGetDaily(): void
    {
        $this->artisan('jav:onejav daily')->assertExitCode(0);
        self::assertIsInt(Onejav::all()->count());
        self::assertGreaterThan(1, Onejav::all()->count());
    }

    public function testGetFully(): void
    {
        $this->artisan('jav:onejav fully')->assertExitCode(0);
        self::assertIsInt(Onejav::all()->count());
        self::assertGreaterThan(1, Onejav::all()->count());

        $movies = JavMovie::all();
        self::assertGreaterThan(1, $movies->count());
        self::assertNotNull($movies->first()->release_date);
        self::assertNotNull($movies->first()->dvd_id);
        self::assertEquals(1, $movies->first()->is_downloadable);
        self::assertEquals(2, CrawlerEndpoints::where(['crawler'=>'Onejav'])->first()->page);
    }
}
