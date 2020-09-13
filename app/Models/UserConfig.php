<?php

namespace App\Models;

use App\Services\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserConfig
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @package App\Models
 */
class UserConfig extends Model
{
    public const NAME = 'name';
    public const VALUE = 'value';
    public const USER_ID = 'user_id';

    public $table = 'user_config';

    /**
     * @param  array  $values
     */
    public function updateConfigs(array $values): void
    {
        $user = Auth::user();

        if ($user->cannot(UserRole::PERMISSION_USER_CONFIG)) {
            return;
        }

        foreach ($values as $key => $value) {
            $this->updateOrInsert(
                [static::USER_ID => $user->id, static::NAME => $key],
                [static::USER_ID => $user->id, static::NAME => $key, static::VALUE => $value]
            );
        }
    }

    public function getAllUserConfigs()
    {
        $user = Auth::user();

        if ($user->cannot(UserRole::PERMISSION_USER_CONFIG)) {
            return null;
        }

        return $this->where(self::USER_ID, '=', $user->id)
            ->get()->keyBy(static::NAME);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->{static::VALUE};
    }
}
