<?php

namespace App\Repositories\Jav;

use App\Models\Jav\JavIdol;

class AnalyticsRepository
{
    public function getOlderAndYoungerIdol(): array
    {
        $idols = JavIdol::orderBy('birthday', 'asc')->get();

        return [
            'older' => $idols->first(),
            'younger' => $idols->last()
        ];
    }
}
