<?php

namespace App\Http\Controllers;

use App\Database\Mongodb;
use App\Facades\UserActivity;
use App\Http\Helpers\Toast;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

abstract class ImagesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected string $title = '';
    protected string $name = '';

    public function dashboard(Request $request)
    {
        $items = $this->getItems($request);

        $covers = array_map(
            static function ($item) {
                return $item->cover;
            },
            $items->items()
        );

        $this->generateMetaTags([], ['og:image' => $covers]);

        return view(
            $this->name.'.index',
            $this->getViewDefaultOptions(
                [
                    'items' => $items,
                    'title' => $this->title,
                ]
            )
        );
    }

    public function item(string $id)
    {
        $item = $this->getItem($id);

        $this->generateMetaTags([], ['og:image' => $item->cover]);

        return view(
            $this->name.'.item',
            [
                'item' => $this->getItem($id),
                'sidebar' => $this->getMenuItems(),
                'title' => $this->title,
            ]
        );
    }

    public function download(string $id)
    {
        if (!$item = $this->getItem($id)) {
            return response()
                ->json(
                    ['html' => Toast::warning('Download', sprintf('Item %s is not available', $id))]
                );
        }

        UserActivity::notify(
            '%s request %s gallery',
            Auth::user(),
            'download',
            [
                \App\Models\Core\UserActivity::OBJECT_ID => $item->_id,
                \App\Models\Core\UserActivity::OBJECT_TABLE => $item->getTable(),
                \App\Models\Core\UserActivity::EXTRA => [
                    'title' => $item->getTitle(),
                    'fields' => [
                        'ID' => $item->_id,
                        'Title' => $item->getTitle(),
                        'Photos count' => count($item->images),
                    ],
                    'footer' => $item->url,
                ],
            ]
        );

        $message = sprintf(
            'Added <span class="badge badge-primary">%s</span> into download queue successfully',
            $item->getTitle()
        );

        $this->processDownload($item);

        return response()->json(['html' => Toast::success('Download', $message)]);
    }

    abstract protected function getItems(Request $request);

    abstract protected function getItem(string $id): Mongodb;

    abstract protected function processDownload(Mongodb $model);
}
