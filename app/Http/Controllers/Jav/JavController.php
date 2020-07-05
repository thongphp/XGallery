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
use App\Models\JavDownload;
use App\Models\JavGenreModel;
use App\Models\JavIdols;
use App\Models\JavMovieModel;
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

    /** @var \App\Repositories\JavMovies */
    protected $repository;

    /**
     * JavController constructor.
     * @param  \App\Repositories\JavMovies  $repository
     */
    public function __construct(\App\Repositories\JavMovies $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function dashboard(Request $request)
    {
        $items = $this->repository->getItems($request->request->all());

        return view(
            'jav.index',
            [
                'items' => $this->repository->getItems($request->request->all()),
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
                'title' => 'JAV '.$movie->item_number,
                'description' => $movie->description,
            ]
        );
    }

    /**
     * @param  int  $id
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function genre(int $id, Request $request)
    {
        $filter = array_merge($request->request->all(), ['genre' => $id]);

        return view(
            'jav.index',
            [
                'items' => app(\App\Repositories\JavMovies::class)->getItems($filter),
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV genre - '.JavGenreModel::find($id)->name,
            ]
        );
    }

    /**
     * @param  int  $id
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function idol(int $id, Request $request)
    {
        $filter = array_merge($request->request->all(), ['idol' => $id]);

        $idol = JavIdols::find($id);

        return view(
            'jav.idol',
            [
                'items' => app(\App\Repositories\JavMovies::class)->getItems($filter),
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
    public function download(string $itemNumber)
    {
        if (JavDownload::where(['item_number' => $itemNumber])->first()) {
            return response()->json([
                'html' => Toast::warning('Download', 'Item <strong>'.$itemNumber.'</strong> already exists')
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
