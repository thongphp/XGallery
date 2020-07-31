<?php

namespace App\Jobs;

use App\Crawlers\Crawler\Xiuren;
use App\Facades\UserActivity;
use App\Jobs\Traits\HasJob;
use App\Repositories\XiurenRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

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
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    public function handle(): void
    {
        $xiurenModel = app(XiurenRepository::class)->findById($this->id);

        if (!$xiurenModel) {
            return;
        }

        UserActivity::notify(
            '[Xiuren] System process for %s action [%s] a gallery',
            Auth::user(),
            'download',
            [
                'object_id' => $xiurenModel->getAttribute('_id'),
                'extra' => [
                    'title' => $xiurenModel->title,
                    'fields' => [
                        'ID' => $xiurenModel->getAttribute('_id'),
                        'Title' => $xiurenModel->title,
                        'Photos count' => count($xiurenModel->images),
                    ],
                    'footer' => $xiurenModel->url,
                ],
            ]
        );

        // @TODO Put download method into model and process it instead
        app(Xiuren::class)->download($xiurenModel);
    }
}
