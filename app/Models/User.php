<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticator;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $remember_token
 * @property string $avatar
 * @property array $configs
 * @package App\Models
 */
class User extends Authenticator
{
    use HasPermissions;
    use HasRoles;

    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const AVATAR = 'avatar';

    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::NAME, self::EMAIL, self::AVATAR
    ];

    /**
     * @param  string  $service
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
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * @param  string  $configKey
     * @param  null|mixed  $default
     *
     * @return mixed
     */
    public function getConfig(string $configKey, $default = null)
    {
        $result = $this->configs()->where(UserConfig::NAME, $configKey)->first();

        return $result ?? $default;
    }

    /**
     * @return HasMany
     */
    public function configs(): HasMany
    {
        return $this->hasMany(UserConfig::class, UserConfig::USER_ID, static::ID);
    }
}
