<?php

namespace App\Models\Jav;

use App\Database\Mongodb;

/**
 * Class R18Model
 * @property string $url
 * @property string $cover
 * @property string $title
 * @property string $categories
 * @property $release_date
 * @property int $runtime
 * @property string $director
 * @property string $studio
 * @property string $label
 * @property string $channel
 * @property string $content_id
 * @property string $dvd_id
 * @property string $series
 * @property string $languages
 * @property array $actresses
 * @property string $sample
 * @property array $gallery
 * @package App\Models\Jav
 */
class R18Model extends Mongodb
{
    public $collection = 'r18';

}
