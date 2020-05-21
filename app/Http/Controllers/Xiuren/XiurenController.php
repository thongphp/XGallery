<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Xiuren;

use App\Http\Controllers\BaseController;
use App\Jobs\XiurenDownload;
use App\Models\Xiuren;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class XiurenController
 * @package App\Http\Controllers\Xiuren
 */
class XiurenController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected \App\Repositories\Xiuren $repository;

    public function __construct(\App\Repositories\Xiuren $repository)
    {
        $this->repository = $repository;
    }

    public function item(string $id)
    {
        return view(
            'xiuren.item',
            [
                'item' => $this->repository->find($id),
                'sidebar' => $this->getMenuItems(),
                'title' => 'Xiuren',
            ]
        );
    }

    /**
     * @param  string  $id
     */
    public function download(string $id)
    {
        XiurenDownload::dispatch($id);
    }
}
