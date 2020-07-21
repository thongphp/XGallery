<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Xiuren;

use App\Facades\UserActivity;
use App\Http\Controllers\BaseController;
use App\Http\Helpers\Toast;
use App\Jobs\XiurenDownload;
use App\Repositories\XiurenRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class XiurenController
 * @package App\Http\Controllers\Xiuren
 */
class XiurenController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const PAGE_TITLE = 'Xiuren';

    /**
     * @param Request $request
     * @param XiurenRepository $repository
     *
     * @return Application|Factory|View
     */
    public function dashboard(Request $request, XiurenRepository $repository)
    {
        return view(
            'xiuren.index',
            $this->getViewDefaultOptions(
                [
                    'items' => $repository->getItems($request),
                    'title' => self::PAGE_TITLE,
                ]
            )
        );
    }

    public function item(string $id, XiurenRepository $repository)
    {
        return view(
            'xiuren.item',
            [
                'item' => $repository->findById($id),
                'sidebar' => $this->getMenuItems(),
                'title' => self::PAGE_TITLE,
            ]
        );
    }

    /**
     * @param string $id
     * @param XiurenRepository $repository
     *
     * @return JsonResponse
     */
    public function download(string $id, XiurenRepository $repository): JsonResponse
    {
        $xiurenModel = $repository->findById($id);

        if (!$xiurenModel) {
            return response()
                ->json(
                    ['html' => Toast::warning('Download', 'Gallery ['.$id.'] is not available.')]
                );
        }

        UserActivity::notify(
            '%s request %s in [XiuRen] gallery',
            Auth::user(),
            'download',
            [
                'object_id' => $xiurenModel->getAttribute('_id'),
                'extra' => [
                    'title' => $xiurenModel->getTitle(),
                    'fields' => [
                        'ID' => $xiurenModel->getAttribute('_id'),
                        'Title' => $xiurenModel->getTitle(),
                        'Photos count' => count($xiurenModel->images),
                    ],
                    'footer' => $xiurenModel->url,
                ],
            ]
        );

        $message = sprintf(
            'Added gallery <span class="badge badge-primary">%s</span> into download queue successfully',
            $xiurenModel->getTitle()
        );

        XiurenDownload::dispatch($id);

        return response()->json(['html' => Toast::success('Download', $message)]);
    }
}
