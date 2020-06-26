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
use App\Repositories\TruyenchonRepository;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

/**
 * Process download each book' chapter
 * @package App\Jobs\Truyenchon
 */
class TruyenchonChapterDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $chapterUrl;

    /**
     * TruyenchonChapterDownload constructor.
     * @param  string  $chapterUrl
     */
    public function __construct(string $chapterUrl)
    {
        $this->chapterUrl = $chapterUrl;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $chapter = app(TruyenchonRepository::class)->getChapterByUrl($this->chapterUrl);
        /**
         * We are using GuzzleHttp instead our Client because it's required for custom header
         */
        $client = new Client();

        $parts = explode('/', $this->chapterUrl);
        $savePath = storage_path('app/truyenchon/'.$parts[4].'/'.$parts[5]);

        if (!File::exists($savePath)) {
            File::makeDirectory($savePath, 0755, true);
        }

        foreach ($chapter->images as $index => $image) {
            $resource = fopen($savePath.'/'.$index.'.jpeg', 'w');
            $client->request('GET', $image, [
                'headers' => [
                    'Cache-Control' => 'no-cache',
                    'Referer' => $chapter->chapterUrl
                ],
                'sink' => $resource,
            ]);
        }
    }
}
