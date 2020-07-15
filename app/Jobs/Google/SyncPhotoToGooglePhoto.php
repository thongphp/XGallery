<?php

namespace App\Jobs\Google;

use App\Exceptions\Google\GooglePhotoApiMediaCreateException;
use App\Exceptions\Google\GooglePhotoApiUploadException;
use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * @package App\Jobs\Google
 */
class SyncPhotoToGooglePhoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $filePath;
    private string $description;
    private string $googleAlbumId;

    /**
     * @param  string  $filePath
     * @param  string  $description
     * @param  string  $googleAlbumId
     */
    public function __construct(string $filePath, string $description, string $googleAlbumId)
    {
        $this->filePath = $filePath;
        $this->description = $description;
        $this->googleAlbumId = $googleAlbumId;

        $this->onQueue(Queues::QUEUE_GOOGLE);
    }

    /**
     * @throws FileNotFoundException
     * @throws GooglePhotoApiMediaCreateException
     * @throws GooglePhotoApiUploadException
     * @throws \JsonException
     */
    public function handle(): void
    {
        if (!Storage::exists($this->filePath)) {
            throw new FileNotFoundException($this->filePath);
        }

        GooglePhotoClient::uploadAndCreateMedia($this->filePath, $this->description, $this->googleAlbumId);
        Storage::delete($this->filePath);
    }
}
