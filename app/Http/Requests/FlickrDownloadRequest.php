<?php

namespace App\Http\Requests;

use App\Facades\Flickr\UrlExtractor;
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
     * @return FlickrUrlInterface|null
     */
    public function getUrl(): ?FlickrUrlInterface
    {
        /**
         * @todo return object with interface
         */
        return UrlExtractor::extract($this->input('url'));
    }
}
