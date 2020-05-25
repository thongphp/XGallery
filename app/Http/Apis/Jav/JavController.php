<?php

namespace App\Http\Apis\Jav;

use App\Http\Controllers\Apis\ApiController;
use App\Repositories\JavMovies;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JavController extends ApiController
{
    /**
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->respondOk(app(JavMovies::class)->getItems($request->request->all())->toArray());
    }
}
