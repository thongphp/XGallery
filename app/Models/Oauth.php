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
 * Class Oauth
 * @property string $remember_token
 * @property array $credential
 * @package App\Models
 *
 * @property string $id;
 */
class Oauth extends Mongodb
{
    public const ID = 'id';

    protected $collection = 'oauths';

    protected $guarded = [];
}
