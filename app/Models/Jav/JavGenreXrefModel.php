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
 * Class JavGenreXref
 * @package App\Models\Jav
 */
class JavGenreXrefModel extends Model
{
    protected $fillable = ['genre_id', 'movie_id'];

    protected $table = 'jav_genres_xref';
}
