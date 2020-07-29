<?php

namespace App\Jobs\Jav;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Jav\JavMovie;
use App\Models\Jav\XCityVideoModel;
use App\Traits\Jav\HasXref;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Process to get XCity video detail
 * @package App\Jobs
 */
class XCityVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;
    use HasXref;

    private string $url;

    /**
     * Create a new job instance.
     *
     * @param  string  $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->onQueue(Queues::QUEUE_JAV);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$itemDetail = app(\App\Crawlers\Crawler\XCityVideo::class)->getItem($this->url)) {
            return;
        }

        XCityVideoModel::updateOrCreate(['item_number' => $itemDetail->item_number], $itemDetail->getAttributes());
        $movie = JavMovie::updateOrCreate(
            ['dvd_id' => $itemDetail->item_number],
            [
                'cover' => $itemDetail->cover,
                'name' => $itemDetail->title,
                'release_date' => $itemDetail->release_date,
                'time' => $itemDetail->time,
                'director' => $itemDetail->director,
                //'studio' => $itemDetail->studio,
                'label' => $itemDetail->label,
                //'content_id' => $itemDetail->content_id,
                'series' => $itemDetail->series,
                'gallery' => $itemDetail->gallery ? json_encode($itemDetail->gallery) : null,
                //'sample' => $itemDetail->sample,
                'description' => $itemDetail->description
            ]
        );

        $this->updateGenres($itemDetail->genres, $movie);
        $this->updateIdols($itemDetail->actresses, $movie);
    }
}
