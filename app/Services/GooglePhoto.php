<?php

namespace App\Services;

use App\Exceptions\Google\GooglePhotoApiCreateAlbumException;
use App\Exceptions\Google\GooglePhotoApiMediaCreateException;
use App\Exceptions\Google\GooglePhotoApiUploadException;
use App\Oauth\GoogleOauthClient;
use App\Repositories\Flickr\PhotoRepository;
use Illuminate\Support\Facades\Storage;

class GooglePhoto extends GoogleOauthClient
{
    private const TITLE_MAX_LENGTH = 500;
    public const ALBUMS_ENDPOINT = 'https://photoslibrary.googleapis.com/v1/albums';

    /**
     * Ref: https://developers.google.com/photos/library/reference/rest/v1/albums/create
     *
     * @param string $title
     *
     * @return object
     *
     * @throws \App\Exceptions\Google\GooglePhotoApiCreateAlbumException
     * @throws \JsonException
     */
    public function createAlbum(string $title): object
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
            throw new GooglePhotoApiCreateAlbumException($title);
        }

        return $content;
    }

    /**
     * https://developers.google.com/photos/library/reference/rest/v1/mediaItems/batchCreate
     *
     * @param string $file
     * @param string $photoId
     * @param string $googleAlbumId
     *
     * @throws \App\Exceptions\Google\GooglePhotoApiMediaCreateException
     * @throws \App\Exceptions\Google\GooglePhotoApiUploadException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \JsonException
     */
    public function uploadAndCreateMedia(string $file, string $photoId, string $googleAlbumId): void
    {
        $photo = app(PhotoRepository::class)->findOrCreateById($photoId);

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
            throw new GooglePhotoApiUploadException($file);
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
            throw new GooglePhotoApiMediaCreateException($uploadToken, $googleAlbumId);
        }

        $newMediaItemResult = $response->newMediaItemResults[0];
        $photo->google_album_id = $googleAlbumId;
        $photo->google_media_id = $newMediaItemResult->mediaItem->id;
        $photo->status = true;
        $photo->save();
    }
}
