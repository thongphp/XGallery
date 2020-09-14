<?php

namespace App\Http\Requests;

use App\Facades\Flickr\UrlExtractor;
use App\Services\Flickr\Objects\FlickrAlbum;
use App\Services\Flickr\Objects\FlickrGallery;
use App\Services\Flickr\Objects\FlickrObjectInterface;
use App\Services\Flickr\Objects\FlickrProfile;
use App\Services\Flickr\Url\FlickrUrlInterface;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class FlickrDownloadRequest
 * @package App\Http\Requests
 */
class FlickrDownloadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules()
    {
        //  @todo Validate url
        return [
            'url' => 'required|url',
        ];
    }

    /**
     * @return FlickrObjectInterface|null
     */
    public function getUrl(): ?FlickrObjectInterface
    {
        $result = UrlExtractor::extract($this->input('url'));

        if (!$result) {
            return null;
        }

        switch ($result->getType()) {
            case FlickrUrlInterface::TYPE_ALBUM:
                return new FlickrAlbum($result);

            case FlickrUrlInterface::TYPE_GALLERY:
                return new FlickrGallery($result);

            case FlickrUrlInterface::TYPE_PROFILE:
                return new FlickrProfile($result);

            default:
                return null;
        }
    }
}
