<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 *
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Jav;

use App\Http\Controllers\BaseController;
use App\Http\Helpers\Toast;
use App\Models\Jav\JavMovie;
use App\Repositories\ConfigRepository;
use App\Repositories\JavMoviesRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Class JavController
 *
 * @package App\Http\Controllers
 */
class JavController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param JavMoviesRepository $repository
     *
     * @return Application|Factory|View
     */
    public function dashboard(Request $request, JavMoviesRepository $repository)
    {
        $items = $repository->getItems($request);

        $covers = array_map(
            static function (JavMovie $item) {
                return $item->getCover();
            },
            $items->items()
        );

        $this->generateMetaTags([], ['og:image' => $covers]);

        return view(
            'jav.index',
            [
                'items' => $items,
                'directors' => $repository->populateDirectorOptions(
                    $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_DIRECTOR, [])
                ),
                'studios' => $repository->populateStudioOptions(
                    $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_STUDIO, [])
                ),
                'channels' => $repository->populateChannelOptions(
                    $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_CHANNEL, [])
                ),
                'series' => $repository->populateSeriesOptions(
                    $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_SERIES, [])
                ),
                'idols' => $repository->populateIdolOptions(
                    $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL, [])
                ),
                'genres' => $repository->populateGenreOptions(
                    $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_GENRE, [])
                ),
                'dateFrom' => $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_FROM, null),
                'dateTo' => $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_TO, null),
                'downloadable' => (boolean) $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_DOWNLOADABLE, false),
                'idolHeight' => $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_HEIGHT, null),
                'idolBreast' => $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_BREAST, null),
                'idolWaist' => $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_WAIST, null),
                'idolHips' => $request->get(ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_HIPS, null),
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$items->total().' Movies - '.$items->currentPage().' / '.$items->lastPage(),
            ]
        );
    }

    /**
     * @param int $id
     *
     * @return Application|Factory|View
     */
    public function movie(int $id)
    {
        $movie = JavMovie::find($id);

        $description = $movie->description ?? $movie->name;
        $this->generateMetaTags(
            [
                'twitter:title' => $movie->name,
                'twitter:description' => $description,
            ],
            [
                'og:title' => $movie->name,
                'og:description' => $description,
                'og:image' => $movie->getCover(),
            ]
        );
        $this->meta->setDescription($description);

        return view(
            'jav.movie',
            [
                'item' => $movie,
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$movie->dvd_id,
                'description' => $movie->description,
            ]
        );
    }

    /**
     * @param string $itemNumber
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function download(string $itemNumber): JsonResponse
    {
        $movie = JavMovie::firstWhere(JavMovie::DVD_ID, '=', $itemNumber);

        if ($movie->isDownloading()) {
            return response()->json(
                [
                    'html' => Toast::warning('Download', 'Item <strong>'.$itemNumber.'</strong> already exists'),
                ]
            );
        }

        $movie->startDownload();

        return response()->json(
            [
                'html' => Toast::success('Download', 'Item <strong>'.$itemNumber.'</strong> added to queue'),
            ]
        );
    }
}
