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
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Request download a book
 * @package App\Jobs\TruyenchonRepository
 */
class TruyenchonDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $id;

    /**
     * Create a new job instance.
     *
     * @param  string  $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->onQueue(Queues::QUEUE_TRUYENTRANH);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware()
    {
        return [new RateLimited('truyenchon')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $model = \App\Models\TruyenchonModel::find($this->id);

        $crawler = app(Truyenchon::class);
        if (!$chapters = $crawler->getItemChapters($model->url)) {
            return;
        }

        $chapters->each(function ($chapter, $index) use ($crawler, $model) {
            if (!$item = $crawler->getItemDetail($chapter)) {
                return;
            }
            TruyenchonChapterDownload::dispatch(
                $item->images->toArray(),
                DIRECTORY_SEPARATOR.Str::slug($model->title).DIRECTORY_SEPARATOR.$index
            )->onQueue('downloads');
        });
    }
}
