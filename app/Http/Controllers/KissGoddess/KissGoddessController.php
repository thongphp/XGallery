<?php

namespace App\Http\Controllers\KissGoddess;

use App\Database\Mongodb;
use App\Http\Controllers\ImagesController;
use App\Models\KissGoddess;
use App\Repositories\KissGoddessRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Request;

class KissGoddessController extends ImagesController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected string $title = 'KissGoddess';
    protected string $name = 'kissgoddess';

    protected function getItems(Request $request)
    {
        return app(KissGoddessRepository::class)->getItems($request);
    }

    protected function getItem(string $id): Mongodb
    {
        return KissGoddess::find($id);
    }
}
