<?php

namespace App\Jobs\Jav;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Jav\JavIdolModel;
use App\Models\JavIdols;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class XCityProfile
 * @package App\Jobs
 */
class XCityProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $url;

    /**
     * XCityProfile constructor.
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
        if (!$itemDetail = app(\App\Crawlers\Crawler\XCityProfile::class)->getItem($this->url)) {
            return;
        }

        \App\Models\Jav\XCityProfile::updateOrCreate(['url' => $itemDetail->url], $itemDetail->getAttributes());

        JavIdolModel::updateOrCreate(
            ['name' => $itemDetail->name],
            [
                'name' => $itemDetail->name,
                'cover' => $itemDetail->cover,
                'blood_type' => $itemDetail->blood_type,
                'city' => $itemDetail->city,
                'height' => $itemDetail->height,
                'breast' => $itemDetail->breast,
                'waist' => $itemDetail->waist,
                'hips' => $itemDetail->hips,
                'favorite' => $itemDetail->favorite,
                'reference_url' => -1 // @todo Maybe remove
            ]
        );
    }
}
