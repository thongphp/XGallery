<?php

namespace App\Console\Commands\Flickr;

use App\Console\BaseCommand;
use App\Jobs\Google\BatchAssignMediaToGoogleAlbum;
use App\Models\Flickr\FlickrDownloadModel;
use App\Repositories\Flickr\PhotoRepository;
use App\Services\Google\Objects\Media;
use Illuminate\Database\Eloquent\Collection;

class FlickrBatchCreateMedia extends BaseCommand
{
    private const BATCH_LIMIT = 1000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flickr:batch-assign-media {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run batch assign media token into correct album on Google Photos';

    /**
     * @return bool
     */
    public function fully(): bool
    {
        /** @var Collection $photosHasToken */
        $photosHasToken = FlickrDownloadModel::whereNotNull(FlickrDownloadModel::GOOGLE_PHOTO_TOKEN)
            ->select(
                [
                    '_id',
                    FlickrDownloadModel::PHOTO_ID,
                    FlickrDownloadModel::GOOGLE_ALBUM_ID,
                    FlickrDownloadModel::GOOGLE_PHOTO_TOKEN,
                ]
            )
            ->orderBy(FlickrDownloadModel::UPDATED_AT, 'asc')
            ->limit(self::BATCH_LIMIT)
            ->get('_id');

        if (!$photosHasToken->count()) {
            return true;
        }

        $photoRepository = app(PhotoRepository::class);

        $results = [];
        foreach ($photosHasToken as $photo) {
            $googleAlbumId = $photo[FlickrDownloadModel::GOOGLE_ALBUM_ID];

            if (!isset($results[$googleAlbumId])) {
                $results[$googleAlbumId] = [];
            }

            $mediaItem = new Media(
                $photo['_id'],
                $photoRepository->findOrCreateByIdWithData(['id' => $photo[FlickrDownloadModel::PHOTO_ID]])->title,
                $photo[FlickrDownloadModel::PHOTO_ID],
                $photo[FlickrDownloadModel::GOOGLE_PHOTO_TOKEN]
            );

            $results[$googleAlbumId][] = $mediaItem;
        }

        foreach ($results as $googleAlbumId => $mediaItems) {
            BatchAssignMediaToGoogleAlbum::dispatch($googleAlbumId, $mediaItems);
        }

        return true;
    }
}
