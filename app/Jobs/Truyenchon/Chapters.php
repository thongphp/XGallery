<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Truyenchon;

use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Truyenchon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Get and save chapter items
 * @package App\Jobs\Truyenchon
 */
class Chapters implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private array  $item;
    private array  $chapterUrls;

    /**
     * Create a new job instance.
     *
     * @param  array  $item
     * @param  array  $chapterUrls
     */
    public function __construct(array $item, array $chapterUrls)
    {
        $this->item = $item;
        $this->chapterUrls = $chapterUrls;
        $this->onQueue(Queues::QUEUE_TRUYENTRANH);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware()
    {
        return [new RateLimited('truyenchon')];
    }

    public function handle()
    {
        $chapters = [];
        /**
         * @var Truyenchon $item
         */
        if (!$item = Truyenchon::where(['url' => $this->item['url']])->first()) {
            return;
        }

        foreach ($this->chapterUrls as $chapterUrl) {
            $chapter = explode('/', $chapterUrl);
            if (!$itemDetail = app(\App\Crawlers\Crawler\Truyenchon::class)->getItemDetail($chapterUrl)) {
                return;
            }

            $item->drop($chapter[5]); // Remove chapter-xxx
            $chapters[$chapter[5]] = $itemDetail->images->toArray();
        }

        $item->chapters = array_merge($item['chapters'] ?? [], $chapters);
        $item->save();
    }
}
