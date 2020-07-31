<?php

namespace App\Jobs\KissGoddess;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\KissGoddessModel;
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

    private KissGoddessModel $item;

    /**
     * @param  KissGoddessModel  $item
     */
    public function __construct(KissGoddessModel $item)
    {
        $this->item = $item;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    public function handle(): void
    {
        $this->item->download();
    }
}
