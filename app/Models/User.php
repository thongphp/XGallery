<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticator;

/**
 * Class User
 * @property string $name
 * @property string $email
 * @property int $id
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
        'name', 'email',
    ];

    /**
     * @param  string  $service
     * @return Oauth|null
     */
    public function getOauth(string $service): ?Oauth
    {
        return app(Oauth::class)->firstWhere(['user_id' => $this->id, 'service' => $service]);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return in_array($this->email, config('services.authenticated.emails'), true);
    }
}
