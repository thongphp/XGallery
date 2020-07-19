<?php

namespace App\Repositories;

use App\Models\Jav\JavIdolModel;
use App\Objects\Option;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class JavIdolsRepository
{
    private array $filterFields = [
        'name', 'alias', 'blood_type', 'city',
    ];

    /**
     * @param Request $request
     *
     * @return LengthAwarePaginator
     */
    public function getItems(Request $request): LengthAwarePaginator
    {
        /** @var Builder $builder */
        $builder = app(JavIdolModel::class)->query();

        if ($keyword = $request->get('keyword')) {
            $builder->where(function ($query) use ($keyword) {
                foreach ($this->filterFields as $filterField) {
                    $query->orWhere($filterField, 'LIKE', '%'.$keyword.'%');
                }
            });
        }

        return $builder->orderBy(
            $request->get(ConfigRepository::KEY_SORT_BY, 'id'),
            $request->get(ConfigRepository::KEY_SORT_DIRECTION, 'desc')
        )
            ->paginate($request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except('page', '_token'));
    }
}
