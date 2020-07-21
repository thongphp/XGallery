<?php

namespace App\Http\Controllers\KissGoddess;

use App\Facades\UserActivity;
use App\Http\Controllers\BaseController;
use App\Http\Helpers\Toast;
use App\Jobs\KissGoddess\KissGoddessDownload;
use App\Repositories\KissGoddessRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

class KissGoddessController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const PAGE_TITLE = 'Kissgoddess';

    /**
     * @param Request $request
     * @param KissGoddessRepository $repository
     *
     * @return Application|Factory|View
     */
    public function dashboard(Request $request, KissGoddessRepository $repository)
    {
        return view(
            'kissgoddess.index',
            $this->getViewDefaultOptions(
                [
                    'items' => $repository->getItems($request),
                    'title' => self::PAGE_TITLE,
                ]
            )
        );
    }

    /**
     * @param string $id
     * @param KissGoddessRepository $repository
     *
     * @return Application|Factory|View
     */
    public function item(string $id, KissGoddessRepository $repository)
    {
        return view(
            'kissgoddess.item',
            [
                'item' => $repository->findById($id),
                'sidebar' => $this->getMenuItems(),
                'title' => self::PAGE_TITLE,
            ]
        );
    }

    /**
     * @param string $id
     * @param KissGoddessRepository $repository
     *
     * @return JsonResponse
     */
    public function download(string $id, KissGoddessRepository $repository): JsonResponse
    {
        $kissGoddessModel = $repository->findById($id);

        if (!$kissGoddessModel) {
            return response()
                ->json(
                    ['html' => Toast::warning('Download', 'Gallery ['.$id.'] is not available.')]
                );
        }

        UserActivity::notify(
            '%s request %s in Adult::KissGoddess gallery',
            Auth::user(),
            'download',
            [
                'object_id' => $kissGoddessModel->getAttribute('_id'),
                'extra' => [
                    'title' => $kissGoddessModel->title,
                    'fields' => [
                        'ID' => $kissGoddessModel->getAttribute('_id'),
                        'Title' => $kissGoddessModel->title,
                        'Photos count' => count($kissGoddessModel->images),
                    ],
                    'footer' => $kissGoddessModel->url,
                ],
            ]
        );

        $message = sprintf(
            'Added gallery <span class="badge badge-primary">%s</span> into download queue successfully',
            $kissGoddessModel->title
        );

        KissGoddessDownload::dispatch($id);

        return response()->json(['html' => Toast::success('Download', $message)]);
    }
}
