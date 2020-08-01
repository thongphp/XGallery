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
use App\Http\Helpers\Toast;
use App\Jobs\Truyenchon\TruyenchonStoryDownload;
use App\Models\Truyenchon\Truyenchon;
use App\Repositories\TruyenchonRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class TruyenchonController
 * @package App\Http\Controllers
 */
class TruyenchonController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param TruyenchonRepository $repository
     *
     * @return Application|Factory|View
     */
    public function dashboard(TruyenchonRepository $repository)
    {
        return view(
            'truyenchon.index',
            $this->getViewDefaultOptions(
                [
                    'items' => $repository->getItems(),
                    'title' => 'Truyenchon',
                ]
            )
        );
    }

    /**
     * @param string $id
     * @param string $chapter
     *
     * @return Application|Factory|View
     */
    public function story(string $id, string $chapter)
    {
        /** @var Truyenchon $story */
        $story = Truyenchon::find($id);
        $keys = [];
        foreach ($story->chapters as $item) {
            $keys[] = $item->chapter;
        }
        $position = array_search($chapter, $keys);
        $nextKey = $keys[$position - 1] ?? null;
        $prevKey = $keys[$position + 1] ?? null;

        return view(
            'truyenchon.story',
            [
                'story' => $story,
                'items' => $story->chapters[$position]->images,
                'next' => $nextKey,
                'prev' => $prevKey,
                'sidebar' => $this->getMenuItems(),
                'title' => 'Truyenchon - '.$story->title,
            ]
        );
    }

    /**
     * @param string $id
     *
     * @return JsonResponse
     */
    public function download(string $id): JsonResponse
    {
        $story = Truyenchon::find($id);
        $message = sprintf(
            'Added story <span class="badge badge-primary">%s</span> into download queue successfully',
            $story->title
        );
        TruyenchonStoryDownload::dispatch($id);

        return response()->json(['html' => Toast::success('Download', $message)]);
    }
}
