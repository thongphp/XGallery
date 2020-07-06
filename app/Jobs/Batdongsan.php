<?php

namespace App\Jobs;

use App\Jobs\Traits\HasJob;
use App\Models\BatdongsanModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class Batdongsan
 * @package App\Jobs
 */
class Batdongsan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $url;

    /**
     * Create a new job instance.
     *
     * @param  string  $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->onQueue(Queues::QUEUE_BATDONGSAN);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$itemDetail = app(\App\Crawlers\Crawler\Batdongsan::class)->getItem($this->url)) {
            return;
        }

        BatdongsanModel::updateOrCreate(['url' => $this->url], $itemDetail->getAttributes());
    }
}
