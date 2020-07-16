<?php

namespace App\Repositories\Jav;

use App\Models\Jav\JavIdolModel;

class AnalyticsRepository
{
    public function getOlderAndYoungerIdol(): array
    {
        $idols = JavIdolModel::orderBy('birthday', 'asc')->get();

        return [
            'older' => $idols->first(),
            'younger' => $idols->last()
        ];
    }
}
