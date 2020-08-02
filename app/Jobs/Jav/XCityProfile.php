<?php

namespace App\Jobs\Jav;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Jav\JavIdol;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class XCityProfileModel
 * @package App\Jobs
 */
class XCityProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $url;

    /**
     * XCityProfileModel constructor.
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
    public function handle(): void
    {
        if (!$itemDetail = app(\App\Crawlers\Crawler\XCityProfile::class)->getItem($this->url)) {
            return;
        }

        \App\Models\Jav\XCityProfile::updateOrCreate(['url' => $itemDetail->url], $itemDetail->getAttributes());
        JavIdol::updateOrCreate(
            ['name' => $itemDetail->name],
            [
                'name' => $itemDetail->name,
                'cover' => $itemDetail->cover,
                'birthday' => $itemDetail->birthday,
                'blood_type' => $itemDetail->blood_type,
                'city' => $itemDetail->city,
                'height' => $itemDetail->height,
                'breast' => $itemDetail->breast,
                'waist' => $itemDetail->waist,
                'hips' => $itemDetail->hips,
                'favorite' => $itemDetail->favorite,
            ]
        );
    }
}
