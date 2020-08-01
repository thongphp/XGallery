<?php

namespace App\Jobs;

use App\Jobs\Traits\HasJob;
use App\Models\KissGoddess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class KissGoddessDownload
 * @package App\Jobs
 */
class KissGoddessDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private KissGoddess $item;

    /**
     * @param  KissGoddess  $item
     */
    public function __construct(KissGoddess $item)
    {
        $this->item = $item;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    public function handle(): void
    {
        $this->item->download();
    }
}
