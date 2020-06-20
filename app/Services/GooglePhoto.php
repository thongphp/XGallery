<?php

namespace App\Services;

use App\Oauth\GoogleOauthClient;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GooglePhoto extends GoogleOauthClient
{
    private const LOG_NAME = 'google';
    private const TITLE_MAX_LENGTH = 500;
    public const ALBUMS_ENDPOINT = 'https://photoslibrary.googleapis.com/v1/albums';

    /**
     * Ref: https://developers.google.com/photos/library/reference/rest/v1/albums/create
     *
     * @param string $title
     *
     * @return object|null
     */
    public function createAlbum(string $title): ?object
    {
        $title = substr($title, 0, self::TITLE_MAX_LENGTH);
        $content = $this->request(
            'POST',
            static::ALBUMS_ENDPOINT,
            [
                'headers' => [
                    'content-type' => 'application/json',
                ],
                'body' => json_encode([
                    'album' => [
                        'title' => $title,
                        'isWriteable' => true,
                    ],
                ], JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT)
            ]
        );

        if (!$content) {
            Log::stack([self::LOG_NAME])->warning('Request responded with no content');
            return null;
        }

        return $content;
    }

    /**
     * https://developers.google.com/photos/library/guides/upload-media
     *
     * @param string $file
     * @param string $photoId
     * @param string $googleAlbumId
     *
     * @return bool
     * @throws \JsonException
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function uploadAndCreateMedia(string $file, string $photoId, string $googleAlbumId): bool
    {
        $photo = app(PhotoRepository::class)->findById($photoId);

        if (!$photo) {
            return false;
        }

        $uploadToken = $this->request(
            'post',
            'https://photoslibrary.googleapis.com/v1/uploads',
            [
                'headers' => [
                    'Content-type' => 'application/octet-stream',
                    'X-Goog-Upload-Content-Type' => Storage::mimeType($file),
                    'X-Goog-Upload-Protocol' => 'raw',
                ],
                'body' => Storage::get($file)
            ],
        );

        if (!$uploadToken) {
            Log::stack([self::LOG_NAME])->alert('Can not add uploading media. PhotoId: '.$photo->id);
            return false;
        }

        $response = $this->request(
            'post',
            'https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate',
            [
                'headers' => ['Content-type' => 'application/json'],
                'body' => json_encode([
                    'albumId' => $googleAlbumId,
                    'newMediaItems' => [
                        [
                            'description' => $photo->title,
                            'simpleMediaItem' => [
                                'fileName' => basename($file),
                                'uploadToken' => $uploadToken
                            ],
                        ]
                    ]
                ], JSON_THROW_ON_ERROR)
            ],
        );

        if (!$response) {
            Log::stack([self::LOG_NAME])->alert('Can not add media into FlickrAlbumDownloadQueue. AlbumId: '.$photo->album->id.' / PhotoId: '.$photo->id);
            return false;
        }

        $newMediaItemResult = $response->newMediaItemResults[0];
        $photo->google_album_id = $googleAlbumId;
        $photo->google_media_id = $newMediaItemResult->mediaItem;
        $photo->status = true;
        $photo->save();

        return true;
    }
}
