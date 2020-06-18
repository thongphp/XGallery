<?php

namespace App\Services;

use App\Oauth\GoogleOauthClient;
use App\Repositories\FlickrAlbums;
use App\Repositories\FlickrPhotos;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GooglePhoto extends GoogleOauthClient
{
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
            Log::stack(['oauth'])->warning('Request responded with no content');
            return null;
        }

        return $content;
    }

    /**
     * https://developers.google.com/photos/library/guides/upload-media
     *
     * @param string $file
     * @param string|\App\Models\FlickrPhoto|\Illuminate\Database\Eloquent\Model $photo
     * @param string|\App\Models\FlickrAlbum|\Illuminate\Database\Eloquent\Model $album
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \JsonException
     */
    public function uploadMedia(string $file, $photo, $album): void
    {
        if (is_string($photo)) {
            $photoRepository = app(FlickrPhotos::class);
            $photo = $photoRepository->findById($photo);
        }

        if (is_string($album)) {
            $repository = app(FlickrAlbums::class);
            $album = $repository->findByAlbumId($album);
        }

        $uploadToken = $photo->getAttributeValue('uploadToken');

        if (empty($uploadToken)) {
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
                Log::stack(['google.photo'])->alert('Can not add uploading media. PhotoId: ' . $photo->getAttributeValue('id'));
                return;
            }

            $photo->setAttribute('uploadToken', $uploadToken);
            $photo->save();
        }

        $googleAlbum = (object) $album->getAttributeValue('googleRef');

        $response = $this->request(
            'post',
            'https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate',
            [
                'headers' => ['Content-type' => 'application/json'],
                'body' => json_encode([
                    'albumId' => $googleAlbum->id,
                    'newMediaItems' => [
                        [
                            'description' => $photo->getAttributeValue('title'),
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
            Log::stack(['google.photo'])->alert('Can not add media into Album. AlbumId: ' . $album->getAttributeValue('id') . ' / PhotoId: ' . $photo->getAttributeValue('id'));
            return;
        }

        $newMediaItemResult = $response->newMediaItemResults[0];
        $photo->setAttribute('googleRef', $newMediaItemResult->mediaItem);
        $photo->setAttribute('status', true);
        $photo->save();
    }
}
