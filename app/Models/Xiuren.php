<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use App\Database\Mongodb;
use App\Models\Traits\HasCover;
use App\Services\Client\HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Spatie\Url\Url;

/**
 * Class XiurenModel
 * @property string $url
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
class Xiuren extends Mongodb
{
    use HasCover;

    public $collection = 'xiuren';

    public const URL = 'url';
    public const IMAGES = 'images';

    protected $fillable = ['url', 'cover', 'images'];

    public function getTitle(): string
    {
        return trim(Url::fromString($this->url)->getPath(), '/');
    }

    public function download(): bool
    {
        if (empty($this->images)) {
            return false;
        }

        $httpClient = app(HttpClient::class);

        foreach ($this->images as $image) {
            $httpClient->download($image, 'xiuren'.DIRECTORY_SEPARATOR.$this->getTitle());
        }

        return true;
    }
}
