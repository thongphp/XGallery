<?php

namespace App\Traits\Jav;

use App\Models\Jav\JavGenre;
use App\Models\Jav\JavGenreXref;
use App\Models\Jav\JavIdol;
use App\Models\Jav\JavIdolXref;
use App\Models\Jav\JavMovie;

trait HasXref
{
    protected function updateGenres(array $genres, JavMovie $movie)
    {
        foreach ($genres as $tag) {
            $model = JavGenre::firstOrCreate(['name' => $tag], ['name' => $tag]);
            JavGenreXref::firstOrCreate(['genre_id' => $model->id, 'movie_id' => $movie->id]);
        }
    }

    protected function updateIdols(array $idols, JavMovie $movie)
    {
        foreach ($idols as $actress) {
            $model = JavIdol::firstOrCreate(['name' => $actress]);
            JavIdolXref::firstOrCreate(['idol_id' => $model->id, 'movie_id' => $movie->id]);
        }
    }
}
