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
use App\Notifications\TruyenchonRequestDownloadException;
use App\Repositories\TruyenchonRepository;
use App\Traits\Notifications\HasSlackNotification;
use Campo\UserAgent;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

/**
 * Process download each book' chapter
 * @package App\Jobs\Truyenchon
 */
class TruyenchonChapterDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob, Notifiable, HasSlackNotification;

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
     * @throws \GuzzleHttp\Exception\GuzzleException|\ImagickException
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

        $images = [];
        foreach ($chapter->images as $index => $image) {
            $filePath = $savePath.'/'.$index.'.jpeg';
            $resource = fopen($filePath, 'w');
            try {
                $response = $client->request('GET', $image, [
                    'headers' => [
                        'User-Agent' => UserAgent::random([]),
                        'Cache-Control' => 'no-cache',
                        'Referer' => $chapter->chapterUrl
                    ],
                    'sink' => $resource,
                ]);

                if ($response->getStatusCode() !== Response::HTTP_OK) {
                    continue;
                }

                if (file_exists($filePath)) {
                    $images[] = $filePath;
                }
            } catch (\Exception $exception) {
                $this->notify(new TruyenchonRequestDownloadException($exception, $chapter->chapterUrl));
            }
        }

        if (empty($images)) {
            return;
        }

        $pdf = new \Imagick($images);
        $pdf->setImageFormat('pdf');

        if (!$pdf->writeImages($savePath.'/'.$parts[4].'.pdf', true)) {
            return;
        }

        foreach ($images as $image) {
            unlink($image);
        }

        // @todo Send notification to user
    }
}
