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

/**
 * Class Onejav
 * @property string $title
 * @package App\Models
 */
class Onejav extends Mongodb
{
    public $collection = 'onejav';

    protected $dates = [
        'created_at',
        'updated_at',
        'date'
    ];

    protected $fillable = ['url', 'cover', 'title', 'size', 'date', 'tags', 'description', 'torrent'];
}
