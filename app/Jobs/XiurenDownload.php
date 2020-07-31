<?php

namespace App\Jobs;

use App\Jobs\Traits\HasJob;
use App\Models\XiurenModel;
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

    private XiurenModel $xiuren;

    /**
     * @param  XiurenModel  $xiuren
     */
    public function __construct(XiurenModel $xiuren)
    {
        $this->xiuren = $xiuren;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    public function handle(): void
    {
        $this->xiuren->download();
    }
}
