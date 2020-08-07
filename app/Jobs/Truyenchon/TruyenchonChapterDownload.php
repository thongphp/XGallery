<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Truyenchon;

use App\Exceptions\Truyenchon\TruyenchonChapterDownloadException;
use App\Exceptions\Truyenchon\TruyenchonChapterDownloadWritePDFException;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Truyenchon\TruyenchonChapter;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ImagickException;

/**
 * Process download each book' chapter
 * @package App\Jobs\Truyenchon
 */
class TruyenchonChapterDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private TruyenchonChapter $model;
    private ?string $userId;

    /**
     * @param TruyenchonChapter $truyenchonChapter
     * @param string|null $userId
     */
    public function __construct(TruyenchonChapter $truyenchonChapter, ?string $userId)
    {
        $this->model = $truyenchonChapter;
        $this->userId = $userId;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    /**
     * @throws GuzzleException
     * @throws ImagickException
     * @throws TruyenchonChapterDownloadException
     * @throws TruyenchonChapterDownloadWritePDFException
     */
    public function handle(): void
    {
        $this->model->download($this->userId);
    }
}
