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
use Imagick;
use ImagickException;

/**
 * Process download each book' chapter
 * @package App\Jobs\Truyenchon
 */
class TruyenchonChapterDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private array  $images;
    /**
     * @var string Save to path
     */
    private string $path;

    /**
     * Create a new job instance.
     *
     * @param  array  $images
     * @param  string  $path
     */
    public function __construct(array $images, string $path)
    {
        $this->images = $images;
        $this->path = $path;
        $this->onQueue(Queues::QUEUE_DOWNLOADS);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware()
    {
        return [new RateLimited('truyenchon')];
    }

    /**
     * @throws ImagickException
     */
    public function handle()
    {
        $crawler = app(Truyenchon::class);
        $files = [];
        foreach ($this->images as $image) {
            $files[] = storage_path('app/'.$crawler->download(
                    $image,
                    'truyenchon'.$this->path
                ));
        }

        $chapter = explode('/', $this->path);
        $chapter = end($chapter);

        $pdf = new Imagick($files);
        $pdf->setImageFormat('pdf');
        if (!$pdf->writeImages(storage_path('app/chapter-'.$chapter).'.pdf', true)) {
            return;
        }

        /**
         * Upload file to drive
         */
    }
}
