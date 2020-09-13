<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public const NAME = 'name';
    public const VALUE = 'value';

    public $table = 'config';

    /**
     * @param  array  $values
     */
    public function updateConfigs(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->updateOrInsert(
                [static::NAME => $key],
                [static::NAME => $key, static::VALUE => $value]
            );
        }
    }

    /**
     * @return Config[]|Collection
     */
    public function getConfigs()
    {
        return self::all()->keyBy(self::NAME);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->{static::VALUE};
    }
}
