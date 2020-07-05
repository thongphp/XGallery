<?php

namespace App\Models\Jav;

use App\Database\Mongodb;

/**
 * Class XCityProfile
 * @property string $name
 * @property string $url
 * @property string $cover
 * @package App\Models\Jav
 */
class XCityProfile extends Mongodb
{
    protected $collection = 'xcity_profiles';

    protected $fillable = [
        'name', 'url', 'cover', 'favorite', 'blood_type', 'city', 'height', 'breast', 'waist', 'hips'
    ];
}
