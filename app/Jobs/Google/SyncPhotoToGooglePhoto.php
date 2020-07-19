<?php

namespace App\Jobs\Google;

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
use Spatie\RateLimitedMiddleware\RateLimited;

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
        // @todo variable name does not make sense
        $this->description = $description;
        $this->googleAlbumId = $googleAlbumId;
        $this->onQueue(Queues::QUEUE_GOOGLE);
    }

    public function middleware()
    {
        return [(new RateLimited())
            ->allow(5)
            ->everySeconds(1)
            ->releaseAfterSeconds(30)];
    }

    /**
     * @throws FileNotFoundException
     * @throws \JsonException
     */
    public function handle(): void
    {
        if (!Storage::exists($this->filePath)) {
            throw new FileNotFoundException('File not found '.$this->filePath);
        }

        GooglePhotoClient::uploadMedia($this->filePath, $this->description, $this->googleAlbumId);
        Storage::delete($this->filePath);
    }
}
