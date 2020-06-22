<?php

namespace App\Services;

use App\Exceptions\Flickr\FlickrApiGalleryGetInfoException;
use App\Exceptions\Flickr\FlickrApiAuthorizedUserGetFavouritePhotosException;
use App\Exceptions\Flickr\FlickrApiPeopleGetPhotosException;
use App\Exceptions\Flickr\FlickrApiGalleryGetPhotosException;
use App\Exceptions\Flickr\FlickrApiPhotoGetSizesException;
use App\Exceptions\Flickr\FlickrApiAuthorizedUserGetContactsException;
use App\Exceptions\Flickr\FlickrApiPeopleGetInfoException;
use App\Exceptions\Flickr\FlickrApiPeopleGetInfoInvalidUserException;
use App\Exceptions\Flickr\FlickrApiPeopleGetInfoUserDeletedException;
use App\Exceptions\Flickr\FlickrApiPhotoSetGetPhotosException;
use App\Exceptions\Flickr\FlickrApiPhotoSetsGetInfoException;
use App\Oauth\OauthClient;
use Illuminate\Support\Facades\Log;

class Flickr extends OauthClient
{
    public const REST_ENDPOINT = 'https://api.flickr.com/services/rest';

    private const RESPONSE_STAT_OK = 'ok';

    private const AUTHORIZED_USER_GET_CONTACTS = 'contacts.getList';
    private const AUTHORIZED_USER_GET_FAVOURITES_PHOTOS = 'favorites.getList';
    private const GALLERY_GET_INFO = 'galleries.getInfo';
    private const GALLERY_GET_PHOTOS = 'galleries.getPhotos';
    private const PHOTO_GET_SIZES = 'photos.getSizes';
    private const PHOTOSETS_GET_INFO = 'photosets.getInfo';
    private const PHOTOSETS_GET_PHOTOS = 'photosets.getPhotos';
    private const PEOPLE_GET_INFO = 'people.getInfo';
    private const PEOPLE_GET_PHOTOS = 'people.getPhotos';

    /**
     * @param string $photoSetId
     * @param int|null $page
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiPhotoSetGetPhotosException
     */
    public function getPhotoSetPhotos(string $photoSetId, ?int $page = 1): ?object
    {
        $result = $this->get(self::PHOTOSETS_GET_PHOTOS, ['photoset_id' => $photoSetId, 'page' => (int) $page]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiPhotoSetGetPhotosException($photoSetId);
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param bool $force
     *
     * @return object|null
     */
    public function get(string $method, array $parameters = [], bool $force = false): ?object
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
            ],
            $force
        );

        if (!$content) {
            Log::stack(['oauth'])->warning('Request responded with no content');
            return null;
        }

        if ($content->stat !== self::RESPONSE_STAT_OK) {
            Log::stack(['oauth'])->warning('Flickr request failed');
        }

        return $this->removeContentObject($content);
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

            $content->{$key} = property_exists($value, '_content') ?
                $value->_content : $this->removeContentObject($value);
        }

        return $content;
    }

    /**
     * @param string $id
     * @param int|null $page
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiGalleryGetPhotosException
     */
    public function getGalleryPhotos(string $id, ?int $page = 1): ?object
    {
        $result = $this->get(self::GALLERY_GET_PHOTOS, ['gallery_id' => $id, 'page' => (int) $page]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiGalleryGetPhotosException($id);
    }

    /**
     * @param string $photoId
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiPhotoGetSizesException
     */
    public function getPhotoSizes(string $photoId): ?object
    {
        $result = $this->get(self::PHOTO_GET_SIZES, ['photo_id' => $photoId]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiPhotoGetSizesException($photoId);
    }

    /**
     * @param int|null $page
     *
     * @return object
     * @throws \App\Exceptions\Flickr\FlickrApiAuthorizedUserGetContactsException
     */
    public function getContactsOfCurrentUser(?int $page = 1): ?object
    {
        $result = $this->get(self::AUTHORIZED_USER_GET_CONTACTS, ['page' => $page]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiAuthorizedUserGetContactsException($page);
    }

    /**
     * @param string $userId
     * @param int|null $page
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiAuthorizedUserGetFavouritePhotosException
     */
    public function getFavouritePhotosOfUser(string $userId, ?int $page = 1): ?object
    {
        $result = $this->get(self::AUTHORIZED_USER_GET_FAVOURITES_PHOTOS, ['user_id' => $userId, 'page' => $page]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiAuthorizedUserGetFavouritePhotosException($userId);
    }

    /**
     * @param string $albumId
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiPhotoSetsGetInfoException
     */
    public function getPhotoSetInfo(string $albumId): ?object
    {
        $result = $this->get(self::PHOTOSETS_GET_INFO, ['photoset_id' => $albumId]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiPhotoSetsGetInfoException($albumId);
    }

    /**
     * @param string $id
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiGalleryGetInfoException
     */
    public function getGalleryInformation(string $id): ?object
    {
        $result = $this->get(self::GALLERY_GET_INFO, ['gallery_id' => $id]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiGalleryGetInfoException($id);
    }

    /**
     * @param string $nsid
     * @param bool $force
     *
     * @return \App\Services\Flickr\Response\PeopleResponseInterface|object
     * @throws \App\Exceptions\Flickr\FlickrApiPeopleGetInfoException
     * @throws \App\Exceptions\Flickr\FlickrApiPeopleGetInfoUserDeletedException
     * @throws \App\Exceptions\Flickr\FlickrApiPeopleGetInfoInvalidUserException
     */
    public function getPeopleInfo(string $nsid, bool $force = false)
    {
        $result = $this->get(self::PEOPLE_GET_INFO, ['user_id' => $nsid], $force);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result->person;
        }

        if ($result->code === 1) {
            throw new FlickrApiPeopleGetInfoInvalidUserException($nsid);
        }

        if ($result->code === 5) {
            throw new FlickrApiPeopleGetInfoUserDeletedException($nsid);
        }

        throw new FlickrApiPeopleGetInfoException($nsid);
    }

    /**
     * @param string $userId
     * @param int|null $page
     *
     * @return object|null
     * @throws \App\Exceptions\Flickr\FlickrApiPeopleGetPhotosException
     */
    public function getPeoplePhotos(string $userId, ?int $page = 1): ?object
    {
        $result = $this->get(self::PEOPLE_GET_PHOTOS, ['user_id' => $userId, 'page' => $page]);

        if ($result->stat === self::RESPONSE_STAT_OK) {
            return $result;
        }

        throw new FlickrApiPeopleGetPhotosException($userId);
    }

    /**
     * @param string $nsid
     *
     * @return bool
     */
    public function validateNsId(string $nsid): bool
    {
        return strpos($nsid, '@') !== false;
    }
}
