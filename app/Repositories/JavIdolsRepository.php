<?php

namespace App\Repositories;

use App\Models\Jav\JavIdolModel;
use App\Traits\Jav\HasFilterValues;
use App\Traits\Jav\HasOrdering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class JavIdolsRepository
{
    use HasOrdering, HasFilterValues;

    private array $filterFields = [
        'name', 'alias', 'city',
    ];

    /**
     * @param Request $request
     *
     * @return LengthAwarePaginator
     */
    public function getItems(Request $request): LengthAwarePaginator
    {
        $builder = app(JavIdolModel::class)->query();

        $select = ['jav_idols.*'];

        if ($keyword = $request->get(ConfigRepository::KEY_KEYWORD)) {
            $builder->where(
                function ($query) use ($keyword) {
                    foreach ($this->filterFields as $filterField) {
                        $query->orWhere($filterField, 'LIKE', '%'.$keyword.'%');
                    }
                }
            );
        }

        $this->processFilterValues(
            $builder,
            'jav_idols.height',
            (int) $request->get(ConfigRepository::JAV_IDOLS_FILTER_HEIGHT),
            '>='
        );
        $this->processFilterValues(
            $builder,
            'jav_idols.breast',
            (int) $request->get(ConfigRepository::JAV_IDOLS_FILTER_BREAST),
            '>='
        );
        $this->processFilterValues(
            $builder,
            'jav_idols.waist',
            (int) $request->get(ConfigRepository::JAV_IDOLS_FILTER_WAIST),
            '>='
        );
        $this->processFilterValues(
            $builder,
            'jav_idols.hips',
            (int) $request->get(ConfigRepository::JAV_IDOLS_FILTER_HIPS),
            '>='
        );

        $ageFrom = (int) $request->get(ConfigRepository::JAV_IDOLS_FILTER_AGE_FROM, null);
        $ageTo = (int) $request->get(ConfigRepository::JAV_IDOLS_FILTER_AGE_TO, null);

        if ($ageFrom && $ageTo) {
            $select[] = DB::raw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age');
            $builder->havingRaw(DB::raw('(age >= ' . $ageFrom . ' AND age <= ' . $ageTo . ')'));
        } elseif (!$ageFrom && $ageTo) {
            $select[] = DB::raw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age');
            $builder->havingRaw(DB::raw('(age IS NOT NULL AND age <= ' . $ageTo . ')'));
        } elseif ($ageFrom && !$ageTo) {
            $select[] = DB::raw('TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age');
            $builder->havingRaw(DB::raw('(age IS NOT NULL AND age >= ' . $ageFrom . ')'));
        }

        $this->processOrdering($builder, $request);

        return $builder->select($select)
            ->paginate($request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except('page', '_token'));
    }
}
