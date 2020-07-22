<?php

namespace App\Jobs\KissGoddess;

use App\Crawlers\Crawler\Kissgoddess;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class KissGoddessDownload
 * @package App\Jobs
 */
class KissGoddessDownloadByUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $url;

    /**
     * @param  string  $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    public function handle(): void
    {
        $crawler = app(Kissgoddess::class);
        $crawler->download($crawler->getItem($this->url));
    }
}
