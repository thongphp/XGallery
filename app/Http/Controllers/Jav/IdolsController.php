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
use App\Models\Jav\JavIdolModel;
use App\Repositories\JavIdolRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

class IdolsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @param JavIdolRepository $repository
     *
     * @return Application|Factory|View
     */
    public function idols(Request $request, JavIdolRepository $repository)
    {
        $items = $repository->getItems($request);

        return view(
            'jav.idols.index',
            [
                'items' => $items,
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$items->total().' Idols - '.$items->currentPage().' / '.$items->lastPage(),
            ]
        );
    }

    /**
     * @param int $id
     *
     * @return Application|Factory|View
     */
    public function idol(int $id)
    {
        $idol = JavIdolModel::find($id);

        if (!$idol) {
            return view(
                'jav.idols.index',
                [
                    'message' => 'Oops! Idol not found',
                    'title' => 'JAV - Idol not found',
                    'sidebar' => $this->getMenuItems(),
                ]
            );
        }

        return view(
            'jav.idol',
            [
                'idol' => $idol,
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$idol->name,
            ]
        );
    }
}
