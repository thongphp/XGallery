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
 * Class JavGenreModel
 * @package App\Models\Jav
 *
 * @property string $id
 * @property string $name
 */
class JavGenre extends Model
{
    protected $fillable = ['name'];
}
