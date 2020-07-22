<?php

namespace App\Jobs\Google;

use App\Facades\GooglePhotoClient;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Services\Google\Objects\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * @package App\Jobs\Google
 */
class BatchAssignMediaToGoogleAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private string $googleAlbumId;
    private array $mediaItems;

    /**
     * @param string $googleAlbumId
     * @param Media[] $mediaItems
     */
    public function __construct(string $googleAlbumId, array $mediaItems)
    {
        $this->googleAlbumId = $googleAlbumId;
        $this->mediaItems = $mediaItems;
        $this->onQueue(Queues::QUEUE_GOOGLE);
    }

    public function handle(): void
    {
        GooglePhotoClient::batchAssignMediaItemsToAlbum($this->googleAlbumId, $this->mediaItems);
    }
}
