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
use App\Repositories\TruyenchonRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get detail of a chapter
 * @package App\Jobs\Truyenchon
 */
class Chapters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $chapterUrl;

    /**
     * Chapters constructor.
     * @param  string  $chapterUrl
     */
    public function __construct(string $chapterUrl)
    {
        $this->chapterUrl = $chapterUrl;
        $this->onQueue(Queues::QUEUE_TRUYENTRANH);
    }

    public function handle()
    {
        if (!$chapter =  app(TruyenchonRepository::class)->getChapterByUrl($this->chapterUrl)) {
            return;
        }

        $detail = app(Truyenchon::class)->getItemDetail($this->chapterUrl);
        $chapter->images = $detail->images->toArray();
        $chapter->save();
    }
}
