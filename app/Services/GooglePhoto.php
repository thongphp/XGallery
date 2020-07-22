<?php

namespace App\Services;

use App\Exceptions\Google\GooglePhotoApiAlbumCreateException;
use App\Exceptions\Google\GooglePhotoApiMediaCreateException;
use App\Exceptions\Google\GooglePhotoApiUploadException;
use App\Exceptions\OAuthClientException;
use App\Models\Flickr\FlickrDownloadModel;
use App\Oauth\GoogleOauthClient;
use App\Services\Google\Objects\Media;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use JsonException;

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
     * @throws GooglePhotoApiAlbumCreateException
     * @throws GuzzleException
     * @throws OAuthClientException
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
                'body' => json_encode(
                    [
                        'album' => [
                            'title' => $title,
                            'isWriteable' => true,
                        ],
                    ],
                    JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT
                ),
            ]
        );

        if (!$content) {
            throw new GooglePhotoApiAlbumCreateException($title);
        }

        return $content;
    }

    /**
     * @param FlickrDownloadModel $flickrDownload
     *
     * @throws FileNotFoundException
     * @throws GooglePhotoApiUploadException
     * @throws GuzzleException
     * @throws OAuthClientException
     */
    public function uploadMedia(FlickrDownloadModel $flickrDownload): void
    {
        // @todo Verify supported format https://developers.google.com/photos/library/guides/upload-media
        $uploadToken = $this->request(
            'post',
            'https://photoslibrary.googleapis.com/v1/uploads',
            [
                'headers' => [
                    'Content-type' => 'application/octet-stream',
                    'X-Goog-Upload-Content-Type' => Storage::mimeType($flickrDownload->local_path),
                    'X-Goog-Upload-Protocol' => 'raw',
                ],
                'body' => Storage::get($flickrDownload->local_path),
            ],
        );

        if (!$uploadToken) {
            throw new GooglePhotoApiUploadException($flickrDownload->local_path, $uploadToken);
        }

        Storage::delete($flickrDownload->local_path);

        $flickrDownload->google_photo_token = $uploadToken;
        $flickrDownload->local_path = null;
        $flickrDownload->save();
    }

    /**
     * https://developers.google.com/photos/library/reference/rest/v1/mediaItems/batchCreate
     * @param string $googleAlbumId
     * @param Media[] $medias
     *
     * @throws GooglePhotoApiMediaCreateException
     * @throws GuzzleException
     * @throws OAuthClientException
     * @throws JsonException
     */
    public function batchAssignMediaItemsToAlbum(string $googleAlbumId, array $medias): void
    {
        $newMediaItems = [];
        foreach ($medias as $media) {
            /** @var Media $media */
            $newMediaItems[] = [
                'description' => $media->getDescription(),
                'simpleMediaItem' => [
                    'fileName' => $media->getFileName(),
                    'uploadToken' => $media->getToken(),
                ],
            ];
        }

        $response = $this->request(
            'post',
            'https://photoslibrary.googleapis.com/v1/mediaItems:batchCreate',
            [
                'headers' => ['Content-type' => 'application/json'],
                'body' => json_encode(
                    [
                        'albumId' => $googleAlbumId,
                        'newMediaItems' => $newMediaItems,
                    ],
                    JSON_THROW_ON_ERROR
                ),
            ],
        );

        if (!$response) {
            throw new GooglePhotoApiMediaCreateException($googleAlbumId, $response);
        }

        $ids = array_map(static function (Media $item) {
            return $item->getDownloadId();
        }, $medias);

        app(FlickrDownloadModel::class)->newModelQuery()->whereIn('_id', $ids)->forceDelete();
    }
}
