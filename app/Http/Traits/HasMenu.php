<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Traits;

use App\Models\MenuItems;

/**
 * Trait HasMenu
 * @package App\Http\Traits
 */
trait HasMenu
{
    /**
     * @return mixed
     */
    protected function getMenuItems()
    {
        return MenuItems::orderBy('ordering', 'asc')->get();
    }
}
