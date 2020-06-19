<?php

namespace App\Services;

use App\Oauth\GoogleOauthClient;
use App\Repositories\FlickrPhotoRepository;
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
     * @param string $photoId
     *
     * @return bool
     * @throws \JsonException
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function uploadAndCreateMedia(string $file, string $photoId): bool
    {
        $photo = app(FlickrPhotoRepository::class)->findById($photoId);

        if (!$photo) {
            return false;
        }

        if (!$photo->uploadToken) {
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
                Log::stack(['google'])->alert('Can not add uploading media. PhotoId: '.$photo->id);
                return false;
            }

            $photo->uploadToken = $uploadToken;
            $photo->save();
        }

        $postBody = [
            'newMediaItems' => [
                [
                    'description' => $photo->title,
                    'simpleMediaItem' => [
                        'fileName' => basename($file),
                        'uploadToken' => $photo->uploadToken
                    ],
                ]
            ]
        ];

        if ($photo->album && $photo->album->googleRef) {
            $googleRef = (object) $photo->album->getAttributeValue('googleRef');
            $postBody['albumId'] = $googleRef->id;
        }

        $response = $this->request(
            'post',
            'https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate',
            [
                'headers' => ['Content-type' => 'application/json'],
                'body' => json_encode($postBody, JSON_THROW_ON_ERROR)
            ],
        );

        if (!$response) {
            Log::stack(['google'])->alert('Can not add media into Album. AlbumId: '.$album->getAttributeValue('id').' / PhotoId: '.$photo->id);
            return false;
        }

        $newMediaItemResult = $response->newMediaItemResults[0];
        $photo->setAttribute('googleRef', $newMediaItemResult->mediaItem);
        $photo->setAttribute('status', true);
        $photo->save();

        return true;
    }
}
