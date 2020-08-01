<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Xiuren;

use App\Database\Mongodb;
use App\Http\Controllers\ImagesController;
use App\Jobs\XiurenDownload;
use App\Models\Xiuren;
use App\Repositories\XiurenRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class XiurenController
 * @package App\Http\Controllers\Xiuren
 */
class XiurenController extends ImagesController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected string $title = 'Xiuren';
    protected string $name = 'xiuren';

    protected function getItems(Request $request)
    {
        return app(XiurenRepository::class)->getItems($request);
    }

    protected function getItem(string $id): Mongodb
    {
        return Xiuren::find($id);
    }

    protected function processDownload($model)
    {
        XiurenDownload::dispatch($model);
    }
}
