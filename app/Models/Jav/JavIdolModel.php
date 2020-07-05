<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Jav;

use App\Models\Traits\HasCover;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JavIdolModel
 * @package App\Models\Jav
 */
class JavIdolModel extends Model
{
    use HasCover;

    protected $fillable = [
        'name', 'alias', 'birthday', 'blood_type', 'city', 'height', 'breast', 'waist', 'hips', 'cover',
        'reference_url', 'favorite'
    ];

    protected $table = 'jav_idols';
}
