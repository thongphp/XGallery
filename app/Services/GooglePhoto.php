<?php

namespace App\Services;

use App\Exceptions\Google\GooglePhotoApiAlbumCreateException;
use App\Exceptions\Google\GooglePhotoApiMediaCreateException;
use App\Exceptions\Google\GooglePhotoApiUploadException;
use App\Oauth\GoogleOauthClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Psr\SimpleCache\InvalidArgumentException;

class GooglePhoto extends GoogleOauthClient
{
    private const TITLE_MAX_LENGTH = 500;
    public const ALBUMS_ENDPOINT = 'https://photoslibrary.googleapis.com/v1/albums';

    /**
     * Ref: https://developers.google.com/photos/library/reference/rest/v1/albums/create
     * @param  string  $title
     * @return object
     * @throws GooglePhotoApiAlbumCreateException
     * @throws GuzzleException
     * @throws \JsonException
     * @throws InvalidArgumentException
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
            throw new GooglePhotoApiAlbumCreateException($title);
        }

        return $content;
    }

    /**
     * https://developers.google.com/photos/library/reference/rest/v1/mediaItems/batchCreate¬
     * @param  string  $file
     * @param  string  $title
     * @param  string  $googleAlbumId
     * @throws GooglePhotoApiMediaCreateException
     * @throws GooglePhotoApiUploadException
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws FileNotFoundException
     * @throws \JsonException
     */
    public function uploadAndCreateMedia(string $file, string $title, string $googleAlbumId): void
    {
        // @todo Verify supported format https://developers.google.com/photos/library/guides/upload-media
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
            throw new GooglePhotoApiUploadException($file, $uploadToken);
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
                            'description' => $title,
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
            throw new GooglePhotoApiMediaCreateException($uploadToken, $googleAlbumId, $response);
        }
    }
}
