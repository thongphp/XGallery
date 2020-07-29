<?php

namespace App\Events;

use App\Models\Jav\JavMovie;

interface JavMovieEventInterface
{

    public function getMovie(): JavMovie;
}
