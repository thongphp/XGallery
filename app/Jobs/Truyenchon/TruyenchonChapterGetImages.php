<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Truyenchon;

use App\Crawlers\Crawler\Truyenchon;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Truyenchon\TruyenchonChapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get detail of a chapter
 * @package App\Jobs\Truyenchon
 */
class TruyenchonChapterGetImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private TruyenchonChapter $chapter;

    /**
     * @param TruyenchonChapter $chapter
     */
    public function __construct(TruyenchonChapter $chapter)
    {
        $this->chapter = $chapter;
        $this->onQueue(Queues::QUEUE_TRUYENTRANH);
    }

    public function handle(): void
    {
        $detail = app(Truyenchon::class)->getItem($this->chapter->chapterUrl);
        $this->chapter->images = $detail->images;
        $this->chapter->save();
    }
}
