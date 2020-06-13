<?php

namespace App\Services;

use App\Oauth\GoogleOauthClient;
use Illuminate\Support\Facades\Log;

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
                ], JSON_FORCE_OBJECT)
            ]
        );

        if (!$content) {
            Log::stack(['oauth'])->warning('Request responded with no content');
            return null;
        }

        return $content;
    }
}
