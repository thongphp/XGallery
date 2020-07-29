<?php

namespace Tests\Feature\Console\Command\Jav;

use App\Models\CrawlerEndpoints;
use App\Models\Jav\JavMovie;
use App\Models\Jav\OnejavModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnejavTest extends TestCase
{
    use RefreshDatabase;

    public function testGetDaily()
    {
        $this->artisan('jav:onejav daily')->assertExitCode(0);
        $this->assertIsInt(OnejavModel::all()->count());
        $this->assertGreaterThan(1, OnejavModel::all()->count());
    }

    public function testGetFully()
    {
        $this->artisan('jav:onejav fully')->assertExitCode(0);
        $this->assertIsInt(OnejavModel::all()->count());
        $this->assertGreaterThan(1, OnejavModel::all()->count());

        $movies = JavMovie::all();
        $this->assertGreaterThan(1, $movies->count());
        $this->assertNotNull($movies->first()->release_date);
        $this->assertNotNull($movies->first()->dvd_id);
        $this->assertEquals(1, $movies->first()->is_downloadable);
        $this->assertEquals(2, CrawlerEndpoints::where(['crawler'=>'Onejav'])->first()->page);
    }
}
