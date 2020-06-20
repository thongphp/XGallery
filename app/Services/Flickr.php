<?php

namespace App\Services;

use App\Oauth\OauthClient;
use Illuminate\Support\Facades\Log;

class Flickr extends OauthClient
{
    public const REST_ENDPOINT = 'https://api.flickr.com/services/rest';

    private const PHOTOSETS_GET_PHOTOS = 'photosets.getPhotos';
    private const ALBUM_GET_INFO = 'photosets.getInfo';
    private const PHOTO_GET_SIZES = 'photos.getSizes';
    private const CONTACT_GET_LIST = 'contacts.getList';
    private const FAVES_GET_LIST = 'favorites.getList';
    private const PEOPLE_GET_INFO = 'people.getInfo';
    private const PEOPLE_GET_PHOTOS = 'people.getPhotos';

    /**
     * @param string $photoSetId
     * @param int|null $page
     *
     * @return object|null
     */
    public function getAlbumPhotos(string $photoSetId, ?int $page = 1): ?object
    {
        return $this->get(self::PHOTOSETS_GET_PHOTOS, ['photoset_id' => $photoSetId, 'page' => (int) $page]);
    }

    /**
     * @param string $method
     * @param array $parameters
     *
     * @return object|null
     */
    public function get(string $method, array $parameters = []): ?object
    {
        $content = $this->request(
            'GET',
            static::REST_ENDPOINT,
            [
                'query' => array_merge(
                    ['method' => 'flickr.'.$method],
                    $this->getDefaultFlickrParameters(),
                    $parameters
                )
            ]
        );

        if (!$content) {
            Log::stack(['oauth'])->warning('Request responded with no content');
            return null;
        }

        if ($content->stat !== 'ok') {
            Log::stack(['oauth'])->warning('Flickr request failed');
            return null;
        }

        return $this->removeContentObject($content);
    }

    /**
     * @param object $content
     *
     * @return object
     */
    private function removeContentObject(object $content): object
    {
        foreach ($content as $key => $value) {
            if (!is_object($value)) {
                continue;
            }

            $content->{$key} = property_exists($value, '_content') ?  $value->_content : $this->removeContentObject($value);
        }

        return $content;
    }

    /**
     * Default parameters for all requests
     *
     * @return array
     */
    private function getDefaultFlickrParameters(): array
    {
        return ['format' => 'json', 'nojsoncallback' => 1, 'api_key' => config('auth.flickr.token')];
    }

    /**
     * @param string $photoId
     *
     * @return object|null
     */
    public function getPhotoSizes(string $photoId): ?object
    {
        return $this->get(self::PHOTO_GET_SIZES, ['photo_id' => $photoId]);
    }

    /**
     * @param int|null $page
     *
     * @return object|null
     */
    public function getContactsOfCurrentUser(?int $page = 1): ?object
    {
        return $this->get(self::CONTACT_GET_LIST, ['page' => $page]);
    }

    /**
     * @param string $userId
     * @param int|null $page
     *
     * @return object|null
     */
    public function getFavouritePhotosOfUser(string $userId, ?int $page = 1): ?object
    {
        return $this->get(self::FAVES_GET_LIST, ['user_id' => $userId, 'page' => $page]);
    }

    /**
     * @param string $albumId
     *
     * @return object|null
     */
    public function getAlbumInfo(string $albumId): ?object
    {
        return $this->get(self::ALBUM_GET_INFO, ['photoset_id' => $albumId]);
    }

    /**
     * @param string $userId
     *
     * @return object|null
     */
    public function getUserInfo(string $userId): ?object
    {
        return $this->get(self::PEOPLE_GET_INFO, ['user_id' => $userId]);
    }

    /**
     * @param string $userId
     * @param int|null $page
     *
     * @return object|null
     */
    public function getUserPhotos(string $userId, ?int $page = 1): ?object
    {
        return $this->get(self::PEOPLE_GET_PHOTOS, ['user_id' => $userId, 'page' => $page]);
    }
}
