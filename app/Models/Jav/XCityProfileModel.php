<?php

namespace App\Models\Jav;

use App\Database\Mongodb;

/**
 * Class XCityProfileModel
 * @property string $name
 * @property string $url
 * @property string $cover
 * @property int $favorite
 * @property string $birthday
 * @property string $blood_type
 * @property string $city
 * @property string $height
 * @property int $breast
 * @property int $waist
 * @property int $hips
 * @property int $foo
 * @package App\Models\Jav
 */
class XCityProfileModel extends Mongodb
{
    protected $collection = 'xcity_profiles';

    protected $fillable = [
        'name', 'url', 'cover', 'favorite', 'blood_type', 'city', 'height', 'breast', 'waist', 'hips'
    ];
}
