<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Dashboard;

use App\Crawlers\Crawler\R18;
use App\Http\Controllers\BaseController;
use App\Repositories\CrawlerEndpoints;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class DashboardController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function dashboard(Request $request)
    {
        return view(
            'dashboard.index',
            $this->getViewDefaultOptions([
                'endpoints' => app(CrawlerEndpoints::class)->getItems(),
                'title' => 'Dashboard',
            ])
        );
    }

    public function login()
    {
        $crawler  = app(R18::class);

        dd($crawler->getIndexPagesCount('https://www.r18.com/videos/vod/movies/list/pagesize=30/price=all/sort=new/type=all/page=1/?dmmref=pc_header'));


        return view(
            'login',
            $this->getViewDefaultOptions(['sidebar' => []])
        );
    }
}
