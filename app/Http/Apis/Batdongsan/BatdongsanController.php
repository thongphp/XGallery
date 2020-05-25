<?php

namespace App\Http\Apis\Batdongsan;

use App\Http\Controllers\Apis\ApiController;
use App\Repositories\BatdongsanRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BatdongsanController extends ApiController
{
    /**
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->respondOk(app(BatdongsanRepository::class)->getItems($request->request->all())->toArray());
    }
}
