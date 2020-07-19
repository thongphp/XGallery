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
use App\Facades\UserActivity;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Mail\TruyenchonDownloadChapterMail;
use App\Repositories\TruyenchonRepository;
use Campo\UserAgent;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Imagick;
use ImagickException;
use Symfony\Component\HttpFoundation\Response;

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
     *
     * @param string $chapterUrl
     */
    public function __construct(string $chapterUrl)
    {
        $this->chapterUrl = $chapterUrl;
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
        $chapterModel = app(TruyenchonRepository::class)->getChapterByUrl($this->chapterUrl);

        if (!$chapterModel) {
            return;
        }

        UserActivity::notify(
            '%s request %s in Comic [Truyenchon] - Chapter',
            Auth::user(),
            'download',
            [
                'object_id' => $chapterModel->getAttribute('_id'),
                'extra' => [
                    'title' => $chapterModel->chapter,
                    // Fields are displayed in a table on the message
                    'fields' => [
                        'ID' => $chapterModel->getAttribute('_id'),
                        'Chapter' => $chapterModel->chapter,
                        'Chapter URL' => $chapterModel->chapterUrl,
                    ],
                    'footer' => $chapterModel->storyUrl,
                ],
            ]
        );

        // We are using GuzzleHttp instead our Client because it's required for custom header
        $client = new Client();

        $parts = explode('/', $this->chapterUrl);
        $savePath = storage_path('app/truyenchon/'.$parts[4].'/'.$parts[5]);

        if (!File::exists($savePath)) {
            File::makeDirectory($savePath, 0755, true);
        }

        $images = [];
        $requestHeaders = [
            'User-Agent' => UserAgent::random([]),
            'Cache-Control' => 'no-cache',
            'Referer' => $chapterModel->chapterUrl,
        ];
        foreach ($chapterModel->images as $index => $image) {
            $filePath = $savePath.'/'.$index.'.jpeg';
            $resource = fopen($filePath, 'wb');

            try {
                $response = $client->request('GET', $image, ['headers' => $requestHeaders, 'sink' => $resource]);

                if ($response->getStatusCode() !== Response::HTTP_OK) {
                    throw new TruyenchonChapterDownloadException(
                        $filePath,
                        $chapterModel->chapterUrl,
                        $response->getBody()
                    );
                }

                if (file_exists($filePath)) {
                    $images[] = $filePath;
                }
            } catch (Exception $exception) {
                throw new TruyenchonChapterDownloadException(
                    $filePath,
                    $chapterModel->chapterUrl,
                    $exception->getMessage()
                );
            }
        }

        if (empty($images)) {
            return;
        }

        $pdf = new Imagick($images);
        $pdf->setImageFormat('pdf');

        $pdfPath = $savePath.'/'.$parts[4].'.pdf';

        if (!$pdf->writeImages($pdfPath, true)) {
            throw new TruyenchonChapterDownloadWritePDFException($chapterModel->chapterUrl, $pdfPath);
        }

        foreach ($images as $image) {
            unlink($image);
        }

        Mail::to(config('mail.to'))
            ->send(new TruyenchonDownloadChapterMail($chapterModel, $pdfPath));

        // @TODO: Consider remove PDF file after send mail.
    }
}
