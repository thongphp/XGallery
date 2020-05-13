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
use App\Models\Truyenchon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TruyenchonController
 * @package App\Http\Controllers
 */
class TruyenchonController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected string $modelClass   = Truyenchon::class;
    protected array  $sortBy       = ['by' => 'id', 'dir' => 'desc'];
    protected array  $filterFields = [
        'title'
    ];

    public function dashboard(Request $request)
    {
        return view(
            'truyenchon.index',
            [
                'items' => $this->getItems($request),
                'sidebar' => $this->getMenuItems(),
                'title' => 'Truyenchon',
                'description' => ''
            ]
        );
    }

    /**
     * @param  string  $id
     * @param  string  $chapter
     * @return Application|Factory|View
     */
    public function story(string $id, string $chapter)
    {
        $story = Truyenchon::find($id);
        $keys = array_keys($story->chapters);
        $position = array_search($chapter, $keys);
        $nextKey = $keys[$position + 1];

        return view(
            'truyenchon.story',
            [
                'story' => $story,
                'items' => $story->chapters[$chapter],
                'next' => $nextKey,
                'sidebar' => $this->getMenuItems(),
                'title' => 'Truyenchon - '.$story->title,
                'description' => ''
            ]
        );
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|View
     */
    public function search(Request $request)
    {
        return view(
            'truyenchon.index',
            [
                'items' => $this->getItems($request),
                'sidebar' => $this->getMenuItems(),
                'title' => 'Truyenchon - Searching by keyword - '.$request->get('keyword'),
                'description' => ''
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
