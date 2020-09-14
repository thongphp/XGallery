<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Truyenchon;

use App\Facades\UserActivity;
use App\Http\Controllers\BaseController;
use App\Http\Helpers\Toast;
use App\Models\Truyenchon\Truyenchon;
use App\Models\Truyenchon\TruyenchonDownload;
use App\Repositories\ConfigRepository;
use App\Repositories\TruyenchonRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TruyenchonController
 * @package App\Http\Controllers
 */
class TruyenchonController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function dashboard(Request $request)
    {
        $items = app(TruyenchonRepository::class)->getItems($request);

        $covers = array_map(
            static function (Truyenchon $item) {
                return $item->getCover();
            },
            $items->items()
        );

        $this->generateMetaTags([], ['og:image' => $covers]);

        return view(
            'truyenchon.index',
            $this->getViewDefaultOptions(
                [
                    'items' => $items,
                    'title' => ucfirst($this->getName()),
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

        $this->generateMetaTags(
            [
                'twitter:title' => $story->title,
                'twitter:description' => $story->description,
            ],
            [
                'og:title' => $story->title,
                'og:description' => $story->description,
                'og:image' => $story->getCover(),
            ]
        );
        $this->meta->setDescription($story->title);

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
        return $this->processDownload($id, false);
    }

    /**
     * @param string $id
     *
     * @return JsonResponse
     */
    public function reDownload(string $id): JsonResponse
    {
        return $this->processDownload($id, true);
    }

    /**
     * @param string $id
     * @param bool $isReDownload
     *
     * @return JsonResponse
     */
    private function processDownload(string $id, bool $isReDownload): JsonResponse
    {
        $downloadModel = TruyenchonDownload::firstOrCreate([
            TruyenchonDownload::STORY_ID => $id,
            TruyenchonDownload::USER_ID => Auth::id(),
        ]);

        if (($isReDownload === true && !$downloadModel->isProcessing())
            || ($isReDownload === false && $downloadModel->isProcessing())) {
            $message = sprintf(
                'Story <span class="badge badge-primary">%s</span> already in download queue',
                $downloadModel->story->title
            );

            return response()->json(['html' => Toast::warning('Download', $message)]);
        }

        $downloadModel->download($isReDownload);

        $message = sprintf(
            'Added story <span class="badge badge-primary">%s</span> into download queue successfully',
            $downloadModel->story->title
        );

        return response()->json(['html' => Toast::success('Download', $message)]);
    }
}
