<?php

namespace App\Repositories;

use App\Models\Core\UserActivity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserActivitiesRepository
{
    private const PER_PAGE = 50;

    private UserActivity $model;

    public function __construct()
    {
        $this->model = app(UserActivity::class);
    }

    /**
     * @param array $filter
     *
     * @return LengthAwarePaginator
     */
    public function getItems(array $filter = []): LengthAwarePaginator
    {
        $builder = $this->model->newQuery();
        $page = request()->except(ConfigRepository::KEY_PAGE);

        unset($filter[ConfigRepository::KEY_PAGE]);

        return $builder->where($filter)
            ->orderBy(UserActivity::CREATED_AT, 'DESC')
            ->paginate(self::PER_PAGE)
            ->appends($page);
    }
}
