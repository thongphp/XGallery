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
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class Oauth
 * @property string $remember_token
 * @package App\Models
 *
 * @property string $id;
 */
class Oauth extends Mongodb implements Authenticatable
{
    protected $collection = 'oauths';

    protected $fillable = ['id'];

    protected $hidden = [
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        // TODO: Implement getAuthPassword() method.
    }

    public function getRememberToken(): string
    {
        return $this->remember_token;
    }

    /**
     * @SuppressWarnings("unused")
     *
     * @param  string  $value
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
