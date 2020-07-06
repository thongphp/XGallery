<?php

namespace App\Models\Jav;

use App\Database\Mongodb;

/**
 * Class XCityVideoModel
 * @property string $title
 * @property string $url
 * @property array $gallery
 * @property array $actresses
 * @property int $favorite
 * @property string $sales_date
 * @property string $label
 * @property string $marker
 * @property string $series
 * @property array $genres
 * @property string $director
 * @property string $item_number
 * @property int $time
 * @property string $release_date
 * @property string $description
 * @package App\Models\Jav
 */
class XCityVideoModel extends Mongodb
{
    protected $collection = 'xcity_videos';

    protected $dates = [
        'created_at',
        'updated_at',
        'release_date',
        'sales_date'
    ];

    protected $fillable = [
        'title', 'url', 'gallery', 'actresses', 'favorite', 'sales_date', 'label', 'marker', 'series', 'genres',
        'director', 'item_number', 'time', 'release_date', 'description'
    ];
}
