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
use App\Models\Traits\HasCover;

/**
 * Class Xiuren
 * @property string $url
 * @property array $cover
 * @property array $images
 * @package App\Models
 */
class XiurenModel extends Mongodb
{
    use HasCover;

    public $collection = 'xiuren';

    protected $fillable = ['url', 'cover', 'images'];
}
