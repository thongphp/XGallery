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
use App\Models\Jav\JavIdol;
use App\Repositories\ConfigRepository;
use App\Repositories\JavIdolsRepository;
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
     * @param JavIdolsRepository $repository
     *
     * @return Application|Factory|View
     */
    public function idols(Request $request, JavIdolsRepository $repository)
    {
        $items = $repository->getItems($request);

        $covers = array_map(
            static function (JavIdol $item) {
                return $item->getCover();
            },
            $items->items()
        );

        $this->generateMetaTags([], ['og:image' => $covers]);

        return view(
            'jav.idols.index',
            [
                'items' => $items,
                'sidebar' => $this->getMenuItems(),
                'title' => 'JAV - '.$items->total().' Idols - '.$items->currentPage().' / '.$items->lastPage(),
                'cities' => $repository->populateCityOptions(
                    $request->get(ConfigRepository::JAV_IDOLS_FILTER_CITY, [])
                ),
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
        $idol = JavIdol::find($id);

        $this->generateMetaTags(
            [
                'twitter:title' => $idol->name,
                'twitter:description' => $idol->name,
            ],
            [
                'og:title' => $idol->name,
                'og:description' => $idol->name,
                'og:image' => $idol->getCover(),
            ]
        );
        $this->meta->setDescription($idol->name);

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
