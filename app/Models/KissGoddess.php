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

/**
 * Class Kissgoddess
 * @property string $url
 * @property string $title
 * @property string $cover
 * @property array $images
 * @package App\Models
 */
class KissGoddess extends Mongodb
{
    use HasCover;

    public $collection = 'kissgoddess';

    protected $fillable = ['url', 'title', 'cover', 'images'];

    public const TITLE = 'title';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function download()
    {
        if (empty($this->images)) {
            return false;
        }

        $httpClient = app(HttpClient::class);

        foreach ($this->images as $image) {
            $httpClient->download($image, 'kissgoddess'.DIRECTORY_SEPARATOR.$this->getTitle());
        }

        return true;
    }
}
