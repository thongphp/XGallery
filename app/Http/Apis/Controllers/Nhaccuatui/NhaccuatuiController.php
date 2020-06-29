<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Apis\Controllers\Nhaccuatui;

use App\Http\Apis\ApiController;
use App\Repositories\NhaccuatuiRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Class NhaccuatuiController
 * @package App\Http\Controllers\Apis\Nhaccuatui
 */
class NhaccuatuiController extends ApiController
{
    /**
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return JsonResponse
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request): JsonResponse
    {
        return $this->respondOk(app(NhaccuatuiRepository::class)->getItems($request->request->all()));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function request(Request $request): JsonResponse
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
