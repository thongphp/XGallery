<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Truyenchon;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Truyenchon\Truyenchon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Request download a book
 * @package App\Jobs\TruyenchonRepository
 */
class TruyenchonStoryDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private Truyenchon $model;
    private ?string $userId;

    /**
     * @param Truyenchon $model
     * @param string|null $userId
     */
    public function __construct(Truyenchon $model, ?string $userId = null)
    {
        $this->model = $model;
        $this->userId = $userId;
        $this->onQueue(Queues::QUEUE_TRUYENTRANH);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->model->download($this->userId);
    }
}
