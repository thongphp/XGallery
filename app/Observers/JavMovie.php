<?php

namespace App\Observers;


use App\Models\JavMovies;

class JavMovie
{
    /**
     * Handle the jav movies "created" event.
     *
     * @param  JavMovies  $javMovies
     * @return void
     */
    public function created(JavMovies $javMovies)
    {
        // @TODO Request 3rd to get extra data of this movie
    }

    /**
     * Handle the jav movies "updated" event.
     *
     * @param  \App\JavMovies  $javMovies
     * @return void
     */
    public function updated(JavMovies $javMovies)
    {
        //
    }

    /**
     * Handle the jav movies "deleted" event.
     *
     * @param  \App\JavMovies  $javMovies
     * @return void
     */
    public function deleted(JavMovies $javMovies)
    {
        //
    }

    /**
     * Handle the jav movies "restored" event.
     *
     * @param  \App\JavMovies  $javMovies
     * @return void
     */
    public function restored(JavMovies $javMovies)
    {
        //
    }

    /**
     * Handle the jav movies "force deleted" event.
     *
     * @param  \App\JavMovies  $javMovies
     * @return void
     */
    public function forceDeleted(JavMovies $javMovies)
    {
        //
    }
}
