<?php

namespace App\Services;

use App\Models\Config as ConfigModel;

class Config
{
    /**
     * @param  string  $name
     * @param  null|mixed  $defaultValue
     *
     * @return mixed|null
     */
    public function getGlobalConfig(string $name, $defaultValue = null)
    {
        $config = ConfigModel::firstWhere(ConfigModel::NAME, '=', $name);

        return null === $config ? $defaultValue : $config->getValue();
    }
}
