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
use App\Models\Jav\OnejavModel;

/**
 * Class JavDownload
 * @package App
 */
class JavDownload extends Mongodb
{
    public function downloads()
    {
        return $this->hasMany(OnejavModel::class, 'title', 'item_number')->orderBy('size', 'desc')->get();
    }
}
