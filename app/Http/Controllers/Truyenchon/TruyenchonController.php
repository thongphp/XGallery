<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Truyenchon;

use App\Http\Controllers\BaseController;
use App\Jobs\Truyenchon\TruyenchonDownload;
use App\Models\TruyenchonModel;
use App\Repositories\TruyenchonRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\View\View;

/**
 * Class TruyenchonController
 * @package App\Http\Controllers
 */
class TruyenchonController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** @var TruyenchonRepository */
    protected $repository;

    public function __construct(TruyenchonRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  string  $id
     * @param  string  $chapter
     * @return Application|Factory|View
     */
    public function story(string $id, string $chapter)
    {
        $story = TruyenchonModel::find($id);
        $keys = array_keys($story->chapters);
        $keys = array_reverse($keys);
        $position = array_search($chapter, $keys);
        $nextKey = $keys[$position + 1] ?? 0;

        return view(
            'truyenchon.story',
            [
                'story' => $story,
                'items' => $story->chapters[$chapter],
                'next' => $nextKey,
                'sidebar' => $this->getMenuItems(),
                'title' => 'Truyenchon - '.$story->title,
            ]
        );
    }

    /**
     * @param  string  $id
     */
    public function download(string $id)
    {
        TruyenchonDownload::dispatch($id);
    }
}
