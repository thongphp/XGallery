<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Apis\Nhaccuatui;

use App\Http\Controllers\Apis\ApiController;
use App\Repositories\NhaccuatuiRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NhaccuatuiController
 * @package App\Http\Controllers\Apis\Nhaccuatui
 */
class NhaccuatuiController extends ApiController
{
    /**
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return Response
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request)
    {
        return $this->respondOk(app(NhaccuatuiRepository::class)->getItems($request->request->all()));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function request(Request $request)
    {
        $args = [];
        if ($title = $request->get('title')) {
            $args['--title'] = $title;
        }
        if ($singer = $request->get('singer')) {
            $args['--singer'] = $singer;
        }

        Artisan::queue('nhaccuatui', $args);

        return $this->respondOk();
    }
}
