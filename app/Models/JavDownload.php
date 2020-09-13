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
 * @property string $item_number
 * @package App
 */
class JavDownload extends Mongodb
{
    public const ITEM_NUMBER = 'item_number';

    /**
     * @return Collection
     */
    public function downloads(): Collection
    {
        return $this->hasMany(Onejav::class, 'title', static::ITEM_NUMBER)->orderBy('size', 'desc')->get();
    }
}
