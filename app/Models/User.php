<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticator;

/**
 * Class User
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $remember_token
 * @property string $avatar
 * @package App\Models
 */
class User extends Authenticator
{
    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const AVATAR = 'avatar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::NAME, self::EMAIL, self::AVATAR
    ];

    /**
     * @param string $service
     *
     * @return Oauth|null
     */
    public function getOAuth(string $service): ?Oauth
    {
        return Oauth::firstWhere([Oauth::SERVICE => $service, Oauth::USER_ID => $this->{self::ID}]);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->{self::EMAIL}, config('services.authenticated.emails'), true);
    }
}
