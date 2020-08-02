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
use App\Models\Jav\Onejav;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class JavDownload
 * @package App
 */
class JavDownload extends Mongodb
{
    /**
     * @return Collection
     */
    public function downloads(): Collection
    {
        return $this->hasMany(Onejav::class, 'title', 'item_number')->orderBy('size', 'desc')->get();
    }
}
