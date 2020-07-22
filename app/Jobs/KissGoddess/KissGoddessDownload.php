<?php

namespace App\Jobs\KissGoddess;

use App\Crawlers\Crawler\Kissgoddess;
use App\Facades\UserActivity;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Repositories\KissGoddessRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

/**
 * Class KissGoddessDownload
 * @package App\Jobs
 */
class KissGoddessDownload implements ShouldQueue
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
        $kissGoddessModel = app(KissGoddessRepository::class)->findById($this->id);

        if (!$kissGoddessModel) {
            return;
        }

        UserActivity::notify(
            '[KissGoddess] System process for %s action [%s] a gallery',
            Auth::user(),
            'download',
            [
                'object_id' => $kissGoddessModel->getAttribute('_id'),
                'extra' => [
                    'title' => $kissGoddessModel->title,
                    'fields' => [
                        'ID' => $kissGoddessModel->getAttribute('_id'),
                        'Title' => $kissGoddessModel->title,
                        'Photos count' => count($kissGoddessModel->images),
                    ],
                    'footer' => $kissGoddessModel->url,
                ],
            ]
        );

        app(Kissgoddess::class)->download($kissGoddessModel);
    }
}
