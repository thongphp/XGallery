<?php

namespace App\Http\Apis\Batdongsan;

use App\Http\Controllers\Apis\ApiController;
use App\Models\Batdongsan;
use Symfony\Component\HttpFoundation\Response;

class BatdongsanController extends ApiController
{
    /**
     * @return Response
     */
    public function index()
    {
        return $this->respondOk(Batdongsan::paginate(15)->toArray());
    }
}
