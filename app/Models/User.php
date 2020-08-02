<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticator;

/**
 * Class User
 * @property string $name
 * @property string $email
 * @property string $oauth_id
 * @property string $remember_token
 * @package App\Models
 */
class User extends Authenticator
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'oauth_id',
    ];

    /**
     * @return Oauth|null
     */
    public function getGoogleInfo(): ?Oauth
    {
        if (empty($this->oauth_id)) {
            return null;
        }

        return app(Oauth::class)->firstWhere([Oauth::ID => $this->oauth_id]);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->email, config('services.authenticated.emails'), true);
    }
}
