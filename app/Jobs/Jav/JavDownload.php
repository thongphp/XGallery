<?php

namespace App\Jobs\Jav;

use App\Crawlers\Crawler\Onejav;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class JavDownload
 * @package App\Jobs\Jav
 */
class JavDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    protected \App\Models\JavDownload $javDownload;

    /**
     * JavDownload constructor.
     * @param  \App\Models\JavDownload  $javDownload
     */
    public function __construct(\App\Models\JavDownload $javDownload)
    {
        $this->javDownload = $javDownload;
        $this->onQueue(Queues::QUEUE_JAV_DOWNLOADS);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $crawler = app(Onejav::class);
        $pages = $crawler->search([$this->javDownload->item_number]);
        $pages->each(function ($page) use ($crawler) {
            $item = $page->sortByDesc('size')->first();
            $itemDetail = $crawler->getItemDetail($item['url']);
            $crawler->download($itemDetail->torrent, 'onejav');
            $this->javDownload->is_downloaded = true;
            $this->javDownload->save();
            return;
        });
    }
}
