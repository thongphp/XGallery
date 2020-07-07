<?php

namespace App\Traits\Jav;

use App\Models\Jav\JavGenreModel;
use App\Models\Jav\JavGenreXrefModel;
use App\Models\Jav\JavIdolModel;
use App\Models\Jav\JavIdolXrefModel;
use App\Models\Jav\JavMovieModel;

trait HasXref
{
    protected function updateGenres(array $genres, JavMovieModel $movie)
    {
        foreach ($genres as $tag) {
            $model = JavGenreModel::firstOrCreate(['name' => $tag], ['name' => $tag]);
            JavGenreXrefModel::firstOrCreate(['genre_id' => $model->id, 'movie_id' => $movie->id]);
        }
    }

    protected function updateIdols(array $idols, JavMovieModel $movie)
    {
        foreach ($idols as $actress) {
            $model = JavIdolModel::firstOrCreate(['name' => $actress]);
            JavIdolXrefModel::firstOrCreate(['idol_id' => $model->id, 'movie_id' => $movie->id]);
        }
    }
}
