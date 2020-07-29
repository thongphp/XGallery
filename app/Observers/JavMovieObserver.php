<?php

namespace App\Observers;

use App\Events\JavMovieCreated;
use App\Events\JavMovieUpdated;
use App\Models\Jav\JavMovie;
use Illuminate\Support\Facades\Event;

class JavMovieObserver
{
    /**
     * Handle the jav movie "created" event.
     *
     * @param  JavMovie  $javMovie
     * @return void
     */
    public function created(JavMovie $javMovie)
    {
        Event::dispatch(new JavMovieCreated($javMovie));
    }

    /**
     * Handle the jav movie "updated" event.
     *
     * @param  JavMovie  $javMovie
     * @return void
     */
    public function updated(JavMovie $javMovie)
    {
        Event::dispatch(new JavMovieUpdated($javMovie));
    }

    /**
     * Handle the jav movie "deleted" event.
     *
     * @param  JavMovie  $javMovie
     * @return void
     */
    public function deleted(JavMovie $javMovie)
    {
        //
    }

    /**
     * Handle the jav movie "restored" event.
     *
     * @param  JavMovie  $javMovie
     * @return void
     */
    public function restored(JavMovie $javMovie)
    {
        //
    }

    /**
     * Handle the jav movie "force deleted" event.
     *
     * @param  JavMovie  $javMovie
     * @return void
     */
    public function forceDeleted(JavMovie $javMovie)
    {
        //
    }
}
