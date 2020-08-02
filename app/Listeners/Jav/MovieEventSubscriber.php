<?php

namespace App\Listeners\Jav;

use App\Crawlers\Crawler\Onejav;
use App\Events\JavMovieCreated;
use App\Events\JavMovieEventInterface;
use App\Events\JavMovieUpdated;
use App\Facades\UserActivity;
use App\Models\JavDownload;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;

class MovieEventSubscriber
{
    /**
     * @param JavMovieEventInterface $event
     *
     * @throws GuzzleException
     */
    public function handleJavMovie(JavMovieEventInterface $event): void
    {
        $movie = $event->getMovie();

        if (!$movie->is_downloadable) {
            return;
        }

        $downloads = JavDownload::where(['item_number' => $movie->dvd_id]);
        $downloads->each(
            static function (JavDownload $download) {
                if (!$item = $download->downloads()->first()) {
                    return;
                }

                $crawler = app(Onejav::class);
                // Check again to get updated torrent link
                $item = $crawler->getItems($item->url)->first();
                $crawler->getClient()->download($item->torrent, 'onejav');
                $download->forceDelete();

                UserActivity::notify('%s %s video '.$item->title, null, 'downloaded');
            }
        );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @SuppressWarnings("unused")
     *
     * @param Dispatcher $events
     */
    public function subscribe($events): void
    {
        Event::listen(JavMovieCreated::class, self::class.'@handleJavMovie');
        Event::listen(JavMovieUpdated::class, self::class.'@handleJavMovie');
    }
}
