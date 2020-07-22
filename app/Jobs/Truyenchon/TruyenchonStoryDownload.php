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
use App\Models\Truyenchon\TruyenchonChapterModel;
use App\Models\Truyenchon\TruyenchonModel;
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

    private string $id;

    /**
     * Create a new job instance.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->onQueue(Queues::QUEUE_TRUYENTRANH);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $truyenChonStory = TruyenchonModel::find($this->id);
        $chapters = TruyenchonChapterModel::where(['storyUrl' => $truyenChonStory->url])->get();

        foreach ($chapters as $chapter) {
            TruyenchonChapterDownload::dispatch($chapter->chapterUrl);
        }
    }
}