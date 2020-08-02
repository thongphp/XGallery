<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Jav;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Jav\JavMovie;
use App\Traits\Jav\HasXref;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Process movie in R18
 * @package App\Jobs
 */
class R18 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;
    use HasXref;

    private string $url;

    /**
     * R18 constructor.
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
        if (!$itemDetail = app(\App\Crawlers\Crawler\R18::class)->getItem($this->url)) {
            return;
        }

        $attributes = $itemDetail->getAttributes();
        \App\Models\Jav\R18::updateOrCreate(['content_id' => $attributes['content_id']], $attributes);

        // Because R18 have movie detail. We will use update instead firstOrCreate
        $movie = JavMovie::updateOrCreate(
            ['dvd_id' => $itemDetail->dvd_id],
            [
                'cover' => $itemDetail->cover,
                'name' => $itemDetail->title,
                'release_date' => $itemDetail->release_date,
                'time' => $itemDetail->runtime,
                'director' => $itemDetail->director,
                'studio' => $itemDetail->studio,
                'label' => $itemDetail->label,
                'content_id' => $itemDetail->content_id,
                'series' => $itemDetail->series,
                'gallery' => $itemDetail->gallery ? json_encode($itemDetail->gallery) : null,
                'sample' => $itemDetail->sample,
            ]
        );

        $this->updateGenres($itemDetail->categories, $movie);
        $this->updateIdols($itemDetail->actresses, $movie);
    }
}
