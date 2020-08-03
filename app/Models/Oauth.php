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
 * @property string $_id
 * @property array $credential
 * @property string $user_id
 * @property string $service
 * @package App\Models
 */
class Oauth extends Mongodb
{
    public const ID = '_id';
    public const USER_ID = 'user_id';
    public const SERVICE = 'service';
    public const CREDENTIAL = 'credential';

    protected $collection = 'oauths';

    protected $guarded = [];
    protected $fillable = [self::USER_ID];
}
