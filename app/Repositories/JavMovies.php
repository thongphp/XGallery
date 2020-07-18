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
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

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
     * @param  Request  $request
     * @return LengthAwarePaginator
     */
    public function getItems(Request $request)
    {
        $builder = app(JavMovieModel::class)->query();

        if ($keyword = $request->get('keyword')) {
            $builder->where(function ($query) use ($keyword) {
                foreach ($this->filterFields as $filterField) {
                    $query->orWhere($filterField, 'LIKE', '%'.$keyword.'%');
                }
            });
        }

        if ($directors = $request->get('filter_director')) {
            $builder->where(function ($query) use ($directors) {
                foreach ($directors as $director) {
                    $query->orWhere('director', 'LIKE', '%'.$director.'%');
                }
            });
        }

        if ($studios = $request->get('filter_studios')) {
            $builder->where(function ($query) use ($studios) {
                foreach ($studios as $studio) {
                    $query->orWhere('studio', 'LIKE', '%'.$studio.'%');
                }
            });
        }

        if ($series = $request->get('filter_series')) {
            $builder->where(function ($query) use ($series) {
                foreach ($series as $serie) {
                    $query->orWhere('series', 'LIKE', '%'.$serie.'%');
                }
            });
        }

        // @todo Filter by multi genres & idols

        return $builder->paginate($request->get('perPage', ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except('page', '_token'));
    }

    public function getDirectors()
    {
        return DB::table('jav_movies')->select('director')
            ->whereNotNull('director')
            ->where('director', '<>', '----')
            ->groupBy('director')->get('director');
    }

    public function getStudios()
    {
        return DB::table('jav_movies')->select('studio')
            ->whereNotNull('studio')
            ->groupBy('studio')->get();
    }

    public function getSeries()
    {
        return DB::table('jav_movies')->select('series')
            ->whereNotNull('series')
            ->groupBy('series')->get();
    }
}
