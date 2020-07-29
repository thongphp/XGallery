<?php

namespace App\Listeners\Jav;

use App\Crawlers\Crawler\Onejav;
use App\Events\JavMovieCreated;
use App\Events\JavMovieEventInterface;
use App\Events\JavMovieUpdated;
use App\Facades\UserActivity;
use App\Models\JavDownload;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;

class MovieEventSubscriber
{
    public function handleJavMovie(JavMovieEventInterface $event)
    {
        $movie = $event->getMovie();
        if (!$event->getMovie()->is_downloadable) {
            return;
        }

        $downloads = JavDownload::where(['item_number' => $movie->dvd_id]);
        $downloads->each(function (JavDownload  $download) {
            if (!$item = $download->downloads()->first()) {
                return;
            }

            $crawler = app(Onejav::class);
            // Check again to get updated torrent link
            $item = $crawler->getItems($item->url)->first();
            $crawler->getClient()->download($item->torrent, 'onejav');
            $download->forceDelete();

            UserActivity::notify('%s %s video ' . $item->title, null, 'downloaded');
        });
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     */
    public function subscribe($events)
    {
        Event::listen(JavMovieCreated::class, self::class.'@handleJavMovie');
        Event::listen(JavMovieUpdated::class, self::class.'@handleJavMovie');
    }
}
