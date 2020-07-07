<?php

namespace App\Jobs;

use App\Jobs\Middleware\RateLimited;
use App\Jobs\Traits\HasJob;
use App\Models\XiurenModel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class XiurenDownload
 * @package App\Jobs
 */
class XiurenDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->onQueue(Queues::QUEUE_JAV_DOWNLOADS);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware()
    {
        return [new RateLimited('xiuren')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $item = XiurenModel::find($this->id);
        $name = basename($item->url, '.html');
        $crawler = app(\App\Crawlers\Crawler\Xiuren::class);
        try {
            foreach ($item->images as $image) {
                if (!$crawler->download($image, 'xiuren'.DIRECTORY_SEPARATOR.$name)) {
                    Log::stack(['download'])->warning('Download error '.$image);
                }
            }
        } catch (Exception $exception) {
            Log::stack(['download'])->error($exception->getMessage());
        }
    }
}
