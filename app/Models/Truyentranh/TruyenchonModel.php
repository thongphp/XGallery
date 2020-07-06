<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Truyentranh;

use App\Database\Mongodb;
use App\Models\Traits\HasCover;
use App\Models\Traits\HasUrl;

/**
 * Class TruyenchonModel
 * @property string $url
 * @property array $images
 * @property string $title
 * @property string $description
 * @property int $state
 * @package App\Models\Truyentranh
 */
class TruyenchonModel extends Mongodb
{
    use HasUrl;
    use HasCover;

    const STATE_PROCESSED = 1;

    public $collection = 'truyenchon';

    protected $fillable = ['url', 'cover', 'title'];
}
