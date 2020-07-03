<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JavMoviesXref
 * @package App
 */
class JavMoviesXref extends Model
{
    public const XREF_TYPE_GENRE = 'genre';
    public const XREF_TYPE_IDOL = 'idol';

    protected $fillable = ['xref_id', 'xref_type', 'movie_id'];
}
