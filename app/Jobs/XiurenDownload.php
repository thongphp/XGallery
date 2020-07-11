<?php

namespace App\Jobs;

use App\Crawlers\Crawler\Xiuren;
use App\Jobs\Traits\HasJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class XiurenDownload
 * @package App\Jobs
 */
class XiurenDownload implements ShouldQueue
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
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $crawler->download(app(Xiuren::class)->getItem($this->url));
    }
}
