<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Repositories;

use App\Models\Jav\JavGenreModel;
use App\Models\Jav\JavIdolModel;
use App\Models\Jav\JavMovieModel;
use App\Objects\Option;
use App\Traits\Jav\HasFilterValues;
use App\Traits\Jav\HasOrdering;
use App\Traits\Jav\HasSortOptions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JavMovieModel
 * @package App\Repositories
 */
class JavMoviesRepository
{
    use HasOrdering, HasSortOptions, HasFilterValues;

    public const ID = 'jav_movies.id';

    private array $filterFields = [
        'name', 'content_id', 'dvd_id', 'description', 'director', 'studio', 'label', 'channel', 'series',
    ];

    /**
     * @param Request $request
     *
     * @return LengthAwarePaginator
     */
    public function getItems(Request $request): LengthAwarePaginator
    {
        $builder = app(JavMovieModel::class)->query();

        if ($keyword = $request->get(ConfigRepository::KEY_KEYWORD)) {
            $builder->where(
                function ($query) use ($keyword) {
                    foreach ($this->filterFields as $filterField) {
                        $query->orWhere('jav_movies.'.$filterField, 'LIKE', '%'.$keyword.'%');
                    }
                }
            );
        }

        $this->processFilterValues(
            $builder,
            'jav_movies.director',
            $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_DIRECTOR, []),
        );

        $this->processFilterValues(
            $builder,
            'jav_movies.studio',
            $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_STUDIO, [])
        );

        $this->processFilterValues(
            $builder,
            'jav_movies.series',
            $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_SERIES, [])
        );

        $this->processFilterValues(
            $builder,
            'jav_movies.channel',
            $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_CHANNEL, [])
        );

        $this->processFilterValues(
            $builder,
            'jav_movies.is_downloadable',
            $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_DOWNLOADABLE, null)
        );

        if ($genres = $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_GENRE)) {
            $builder->leftJoin('jav_genres_xref', self::ID, 'jav_genres_xref.movie_id')
                ->whereIn('jav_genres_xref.genre_id', $genres);
        }

        $dateFrom = $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_FROM);
        $dateTo = $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_TO);

        if ($dateFrom && $dateTo) {
            $dateFrom = new Carbon($dateFrom);
            $dateTo = new Carbon($dateTo);
            $builder->orWhereBetween('jav_movies.release_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()]);
        } elseif ($dateFrom && !$dateTo) {
            $dateFrom = new Carbon($dateFrom);
            $builder->orWhere('jav_movies.release_date', '>=', $dateFrom->startOfDay());
        } elseif (!$dateFrom && $dateTo) {
            $dateTo = new Carbon($dateTo);
            $builder->orWhere('jav_movies.release_date', '<=', $dateTo->endOfDay());
        }

        $this->processIdolFilter($builder, $request);
        $this->processOrdering($builder, $request);

        return $builder->select('jav_movies.*')
            ->paginate($request->get('perPage', ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except('page', '_token'));
    }

    /**
     * @param array $selectedOptions
     *
     * @return Option[]
     */
    public function populateDirectorOptions(array $selectedOptions): array
    {
        $directors = DB::table('jav_movies')->select('director')
            ->whereNotNull('director')
            ->where('director', '<>', '----')
            ->groupBy('director')
            ->get('director');

        return $this->sortOptions($directors, $selectedOptions, 'director');
    }

    /**
     * @param array $selectedOptions
     *
     * @return Option[]
     */
    public function populateStudioOptions(array $selectedOptions): array
    {
        $results = DB::table('jav_movies')->select('studio')
            ->whereNotNull('studio')
            ->groupBy('studio')
            ->get();

        return $this->sortOptions($results, $selectedOptions, 'studio');
    }

    /**
     * @param array $selectedOptions
     *
     * @return Option[]
     */
    public function populateSeriesOptions(array $selectedOptions): array
    {
        $results = DB::table('jav_movies')->select('series')
            ->whereNotNull('series')
            ->where('series', '<>', '----')
            ->groupBy('series')
            ->get();

        return $this->sortOptions($results, $selectedOptions, 'series');
    }

    /**
     * @param array $selectedOptions
     *
     * @return Option[]
     */
    public function populateChannelOptions(array $selectedOptions): array
    {
        $results = DB::table('jav_movies')->select('channel')
            ->whereNotNull('channel')
            ->groupBy('channel')->get();

        return $this->sortOptions($results, $selectedOptions, 'series');
    }

    /**
     * @param array $selectedOptions
     *
     * @return Option[]
     */
    public function populateGenreOptions(array $selectedOptions): array
    {
        $results = JavGenreModel::all();

        return $results->map(
            static function (JavGenreModel $item) use ($selectedOptions) {
                return new Option($item->name, $item->id, in_array($item->id, $selectedOptions));
            }
        )
            ->sort(
                static function (Option $itemA, Option $itemB) {
                    if ($itemA->isSelected() !== $itemB->isSelected()) {
                        return !$itemA->isSelected();
                    }

                    return $itemA->getText() <=> $itemB->getText();
                }
            )
            ->toArray();
    }

    /**
     * @param array $selectedOptions
     *
     * @return Option[]
     */
    public function populateIdolOptions(array $selectedOptions): array
    {
        $results = JavIdolModel::all();

        return $results->map(
            static function (JavIdolModel $item) use ($selectedOptions) {
                return new Option($item->name, $item->id, in_array($item->id, $selectedOptions));
            }
        )
            ->sort(
                static function (Option $itemA, Option $itemB) {
                    if ($itemA->isSelected() !== $itemB->isSelected()) {
                        return !$itemA->isSelected();
                    }

                    return $itemA->getText() <=> $itemB->getText();
                }
            )
            ->toArray();
    }

    /**
     * @param Builder $builder
     * @param Request $request
     */
    private function processIdolFilter(Builder $builder, Request $request): void
    {
        $builder->leftJoin('jav_idols_xref', self::ID, 'jav_idols_xref.movie_id')
            ->leftJoin('jav_idols', 'jav_idols.id', 'jav_idols_xref.idol_id');

        $this->processFilterValues(
            $builder,
            'jav_idols_xref.idol_id',
            $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL, [])
        );

        $this->processFilterValues(
            $builder,
            'jav_idols.height',
            (int) $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_HEIGHT),
            '>='
        );
        $this->processFilterValues(
            $builder,
            'jav_idols.breast',
            (int) $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_BREAST),
            '>='
        );
        $this->processFilterValues(
            $builder,
            'jav_idols.waist',
            (int) $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_WAIST),
            '>='
        );
        $this->processFilterValues(
            $builder,
            'jav_idols.hips',
            (int) $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_HIPS),
            '>='
        );
    }
}
