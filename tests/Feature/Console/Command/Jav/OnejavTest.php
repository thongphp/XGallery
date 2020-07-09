<?php

namespace Tests\Feature\Console\Command\Jav;

use App\Models\Jav\JavMovieModel;
use App\Models\Jav\OnejavModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\FlickrMongoDatabase;

class OnejavTest extends TestCase
{
    use RefreshDatabase, FlickrMongoDatabase;

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

        $movies = JavMovieModel::all();
        $this->assertGreaterThan(1, $movies->count());
        $this->assertNotNull($movies->first()->release_date);
        $this->assertNotNull($movies->first()->dvd_id);
        $this->assertEquals(1, $movies->first()->is_downloadable);
    }
}
