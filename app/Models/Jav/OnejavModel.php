<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Jav;

use App\Database\Mongodb;

/**
 * Class OnejavModel
 * @property string $url
 * @property string $cover
 * @property string $title
 * @property int $size
 * @property string $date
 * @property array $tags
 * @property string $description
 * @property array $actresses
 * @property string $torrent
 * @package App\Models\Jav
 */
class OnejavModel extends Mongodb
{
    public $collection = 'onejav';

    protected $dates = [
        'created_at',
        'updated_at',
        'date'
    ];

    protected $fillable = ['url', 'cover', 'title', 'size', 'date', 'tags', 'description', 'torrent', 'actresses'];
}
