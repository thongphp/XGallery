<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class BaseRepository
 * @package App\Repositories
 */
class BaseRepository implements RepositoryInterface
{
    const CACHE_INTERVAL = 3600;
    protected Model                  $model;
    protected Builder                $builder;

    protected string $primaryKey = 'id';

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->builder = $model->query();
    }

    public function getItems(array $filter = [])
    {
        $id = get_class($this).':'.__FUNCTION__.':'.serialize($filter);
        Cache::forget($id);
        if (isset($filter['cache']) && $filter['cache'] === 0) {
            Cache::forget($id);
        }

        $orderBy = $filter['sort-by'] ?? $this->primaryKey;
        $orderDir = $filter['sort-dir'] ?? 'asc';
        $perPage = isset($filter['per-page']) ? (int) $filter['per-page'] : 15;
        $page = request()->except('page');

        unset($filter['cache'], $filter['sort-by'], $filter['sort-dir'], $filter['per-page']);

        return Cache::remember(
            $id,
            self::CACHE_INTERVAL,
            function () use ($page, $perPage, $orderBy, $orderDir, $filter) {
                return $this->builder->where($filter)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($perPage)
                    ->appends($page);
            }
        );
    }

    /**
     * @param $id
     *
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
