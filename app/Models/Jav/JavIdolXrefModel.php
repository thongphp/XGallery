<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Jav;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JavIdolXrefModel
 * @package App\Models\Jav
 */
class JavIdolXrefModel extends Model
{
    protected $fillable = ['idol_id', 'movie_id'];

    protected $table = 'jav_idols_xref';
}
