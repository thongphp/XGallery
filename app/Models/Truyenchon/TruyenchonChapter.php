<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Truyenchon;

use App\Database\Mongodb;
use App\Exceptions\Truyenchon\TruyenchonChapterDownloadException;
use App\Exceptions\Truyenchon\TruyenchonChapterDownloadWritePDFException;
use App\Mail\TruyenchonDownloadChapterMail;
use App\Models\Traits\HasCover;
use App\Models\Traits\HasUrl;
use App\Models\User;
use App\Services\Client\HttpClient;
use Campo\UserAgent;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;

/**
 * Class TruyenchonChapterModel
 * @package App\Models\Truyenchon
 *
 * @property string $_id
 * @property string $storyUrl
 * @property string $chapterUrl
 * @property string $chapter
 * @property string $title
 * @property string $description
 * @property array $images
 */
class TruyenchonChapter extends Mongodb
{
    use HasUrl;
    use HasCover;

    public const STORY_URL = 'storyUrl';
    public const CHAPTER_URL = 'chapterUrl';

    public $collection = 'truyenchon_chapters';

    protected $fillable = [self::STORY_URL, self::CHAPTER_URL, 'chapter', 'title', 'description', 'images'];

    /**
     * @param ?string $userId
     *
     * @throws GuzzleException
     * @throws ImagickException
     * @throws TruyenchonChapterDownloadException
     * @throws TruyenchonChapterDownloadWritePDFException
     */
    public function download(?string $userId): void
    {
        $httpClient = app(HttpClient::class);

        $parts = explode('/', $this->chapterUrl);
        $savePath = storage_path('app/truyenchon/'.$parts[4].'/'.$parts[5]);

        if (!File::exists($savePath)) {
            File::makeDirectory($savePath, 0755, true);
        }

        $images = [];
        $requestHeaders = [
            'User-Agent' => UserAgent::random([]),
            'Cache-Control' => 'no-cache',
            'Referer' => $this->chapterUrl,
        ];

        foreach ($this->images as $index => $image) {
            $filePath = $savePath.'/'.$index.'.jpeg';
            $resource = fopen($filePath, 'wb');

            try {
                $result = $httpClient->download(
                    $image,
                    $savePath,
                    ['headers' => $requestHeaders, 'sink' => $resource]
                );

                if ($result !== false) {
                    $images[] = $filePath;
                }
            } catch (Exception $exception) {
                throw new TruyenchonChapterDownloadException(
                    $savePath.'/'.basename($image).'.jpeg',
                    $this->chapterUrl,
                    $exception->getMessage()
                );
            }
        }

        if (empty($images)) {
            return;
        }

        $relativePdfPath = 'app/truyenchon/'.$parts[4].'/'.$parts[5] . '/' . $parts[4].'.pdf';
        $pdfPath = $savePath.'/'.$parts[4].'.pdf';

        $pdf = new Imagick($images);
        $pdf->setImageFormat('pdf');

        if (!$pdf->writeImages($pdfPath, true)) {
            throw new TruyenchonChapterDownloadWritePDFException($this->chapterUrl, $pdfPath);
        }

        foreach ($images as $image) {
            unlink($image);
        }

        $this->sendMailToUser($pdfPath, $relativePdfPath, $userId);
    }

    /**
     * @param string $pdfPath
     * @param string|null $userId
     */
    private function sendMailToUser(string $pdfPath, string $relativePdfPath, ?string $userId): void
    {
        $user = User::find($userId);
        $mailTo = !$user ? config('mail.to') : $user->email;

        Mail::to($mailTo)
            ->send(new TruyenchonDownloadChapterMail($this, $pdfPath));

        Storage::delete($relativePdfPath);
    }
}
