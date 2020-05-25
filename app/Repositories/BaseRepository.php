<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
class BaseRepository
{
    const CACHE_INTERVAL = 3600;
    protected Model                  $model;
    protected Builder                $builder;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->builder = $model->query();
    }

    public function getItems(array $filter = [])
    {
        $id = get_class($this).':'.__FUNCTION__.':'.serialize($filter);
        if (isset($filter['cache']) && $filter['cache'] == 0) {
            Cache::forget($id);
        }

        return Cache::remember(
            $id,
            self::CACHE_INTERVAL,
            function () use ($filter) {
                $this->builder->orderBy($filter['sort-by'] ?? 'id', $filter['sort-dir'] ?? 'asc');
                return $this->builder->paginate(isset($filter['per-page']) ? (int) $filter['per-page'] : 15)
                    ->appends(request()->except('page'));
            }
        );
    }

    /**
     * @param $id
     * @return Model
     */
    public function find($id): Model
    {
        return Cache::remember(
            get_class($this).':'.__FUNCTION__.':'.$id,
            self::CACHE_INTERVAL,
            function () use ($id) {
                return $this->model->find($id);
            }
        );
    }
}
