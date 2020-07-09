<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Jav;

use App\Http\Controllers\BaseController;
use App\Http\Helpers\Toast;
use App\Models\Jav\JavIdolModel;
use App\Models\Jav\JavMovieModel;
use App\Models\JavDownload;
use App\Repositories\JavMovies;
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
 * @package App\Http\Controllers
 */
class JavController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param JavMovies $repository
     *
     * @return Application|Factory|View
     */
    public function dashboard(Request $request, JavMovies $repository)
    {
        $items = $repository->getItems($request->request->all());

        return view(
            'jav.index',
            [
                'items' => $repository->getItems($request->request->all()),
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$items->total().' Movies - '.$items->currentPage().' / '.$items->lastPage(),
            ]
        );
    }

    /**
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function movie(int $id)
    {
        $movie = JavMovieModel::find($id);

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
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function idol(int $id)
    {
        $idol = JavIdolModel::find($id);

        return view(
            'jav.idol',
            [
                'idol' => $idol,
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$idol->name,
            ]
        );
    }

    /**
     * @param  string  $itemNumber
     * @return JsonResponse
     * @throws Throwable
     */
    public function download(string $itemNumber): JsonResponse
    {
        if (JavDownload::where(['item_number' => $itemNumber])->first()) {
            return response()->json([
                'html' => Toast::warning('Download', 'Item <strong>'.$itemNumber.'</strong> already exists'),
            ]);
        }

        $model = app(JavDownload::class);
        $model->item_number = $itemNumber;
        $model->save();

        return response()->json([
            'html' => Toast::success('Download', 'Item <strong>'.$itemNumber.'</strong> added to queue')
        ]);
    }
}
