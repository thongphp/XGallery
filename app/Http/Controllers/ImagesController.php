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
        return view(
            $this->name.'.index',
            $this->getViewDefaultOptions(
                [
                    'items' => $this->getItems($request),
                    'title' => $this->title
                ]
            )
        );
    }

    public function item(string $id)
    {
        return view(
            $this->name.'.item',
            [
                'item' => $this->getItem($id),
                'sidebar' => $this->getMenuItems(),
                'title' => $this->title
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
                'object_id' => $item->_id,
                'extra' => [
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
