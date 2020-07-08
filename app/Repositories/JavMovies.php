<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Repositories;

use App\Models\Jav\JavMovieModel;
use App\Models\Jav\JavMovieXrefModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class JavMovieModel
 * @package App\Repositories
 */
class JavMovies
{
    private array $filterFields = [
        'name', 'content_id', 'dvd_id', 'director', 'studio', 'label', 'channel', 'series', 'description'
    ];

    /**
     * @param  array  $filter
     * @return LengthAwarePaginator
     */
    public function getItems(array $filter = [])
    {
        $builder = app(JavMovieModel::class)->query();

        if (isset($filter['keyword']) && !empty($filter['keyword'])) {
            $filter['searchBy'] = $filter['searchBy'] ?? 'keyword';
            switch ($filter['searchBy']) {
                case 'keyword':
                    $builder->where(function ($query) use ($filter) {
                        foreach ($this->filterFields as $filterField) {
                            $query = $query->orWhere($filterField, 'LIKE', '%'.$filter['keyword'].'%');
                        }
                    });
                    break;
                default:
                    $builder->where($filter['searchBy'], 'LIKE', '%'.$filter['keyword'].'%');
                    break;
            }
        }

        // @todo Filter by multi genres & idols
        $perPage = isset($filter['per-page']) ? (int) $filter['per-page'] : 15;

        return $builder->paginate($perPage);
    }
}
