<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 *
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Flickr;

use App\Facades\Flickr;
use App\Facades\Flickr\UrlExtractor;
use App\Http\Controllers\BaseController;
use App\Jobs\Flickr\FlickrDownload;
use App\Models\FlickrContacts;
use App\Services\Flickr\Url\FlickrUrlInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FlickrController
 *
 * @package App\Http\Controllers\Flickr
 */
class FlickrController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected \App\Repositories\FlickrContacts $repository;

    /**
     * FlickrController constructor.
     *
     * @param  \App\Repositories\FlickrContacts  $repository
     */
    public function __construct(\App\Repositories\FlickrContacts $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse|void
     */
    public function download(Request $request)
    {
        if (!$url = $request->get('url')) {
            return;
        }

        if (!$result = UrlExtractor::extract($url)) {
            return redirect()
                ->route('flickr.dashboard.view')
                ->with('error', 'Could not detect type of URL');
        }

        switch ($result->getType()) {
            case FlickrUrlInterface::TYPE_ALBUM:
                $photos = Flickr::get('photosets.getPhotos', ['photoset_id' => $result->getId()]);

                if (!$photos) {
                    return redirect()->route('flickr.dashboard.view')->with('error', 'Can not get photosets');
                }

                $flashMessage = 'Added '.count($photos->photoset->photo).' photos of album <strong>'
                    .$photos->photoset->title.'</strong> by <strong>'. $result->getOwner() . '</strong> to queue';

                foreach ($photos->photoset->photo as $photo) {
                    FlickrDownload::dispatch($photos->photoset->owner, $photo);
                }

                if ($photos->photoset->page === 1) {
                    return redirect()->route('flickr.dashboard.view')->with('success', $flashMessage);
                }

                for ($page = 2; $page <= $photos->photoset->pages; $page++) {
                    $photos = Flickr::get('photosets.getPhotos', ['photoset_id' => $url, 'page' => $page]);
                    foreach ($photos->photoset->photo as $photo) {
                        FlickrDownload::dispatch($photos->photoset->owner, $photo);
                    }
                }
                break;

            default:
                return redirect()
                    ->route('flickr.dashboard.view')
                    ->with('error', 'Could not detect type of URL');
                break;
        }

        return redirect()->route('flickr.dashboard.view')->with('success', $flashMessage);
    }

    /**
     * @param  string  $nsid
     *
     * @return Application|Factory|View
     */
    public function contact(string $nsid)
    {
        return view(
            'flickr.photos',
            $this->getViewDefaultOptions([
                'items' => FlickrContacts::where(['nsid' => $nsid])->first()->photos()->paginate(30),
                'title' => 'Flickr',
            ])
        );
    }
}
