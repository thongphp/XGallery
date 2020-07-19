<?php

namespace App\Traits\Jav;

use App\Repositories\ConfigRepository;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Request;

trait HasOrdering
{
    /**
     * @param Builder $builder
     * @param Request $request
     * @param string $defaultColumn
     * @param string $defaultDirection
     */
    protected function processOrdering(
        Builder $builder,
        Request $request,
        string $defaultColumn = 'id',
        string $defaultDirection = 'desc'
    ): void {
        $orderBy = $request->get(ConfigRepository::KEY_SORT_BY, $defaultColumn);
        $orderDirection = $request->get(ConfigRepository::KEY_SORT_DIRECTION, $defaultDirection);

        $builder->orderBy($orderBy, $orderDirection);
    }
}
