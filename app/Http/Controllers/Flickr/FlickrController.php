<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers\Flickr;

use App\Http\Controllers\BaseController;
use App\Jobs\Flickr\FlickrDownload;
use App\Models\FlickrContacts;
use App\Oauth\Services\Flickr\Flickr;
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
 * @package App\Http\Controllers\Flickr
 */
class FlickrController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected \App\Repositories\FlickrContacts $repository;

    /**
     * FlickrController constructor.
     * @param  \App\Repositories\FlickrContacts  $repository
     */
    public function __construct(\App\Repositories\FlickrContacts $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse|void
     */
    public function download(Request $request)
    {
        if (!$url = $request->get('url')) {
            return;
        }

        $flashMessage = '';

        if (strpos($url, 'albums') !== false) {
            $urls = explode('/', $url);
            $url = end($urls);

            $client = app(Flickr::class);
            $photos = $client->get('photosets.getPhotos', ['photoset_id' => $url]);

            if (!$photos) {
                return redirect()->route('flickr.dashboard.view')->with('error', 'Can not get photosets');
            }

            $flashMessage = 'Added '.count($photos->photoset->photo).' photos of album <strong>'
                .$photos->photoset->title.'</strong> to queue';

            foreach ($photos->photoset->photo as $photo) {
                FlickrDownload::dispatch($photos->photoset->owner, $photo);
            }

            if ($photos->photoset->page == 1) {
                return redirect()->route('flickr.dashboard.view')->with('success', $flashMessage);
            }

            for ($page = 2; $page <= $photos->photoset->pages; $page++) {
                $photos = $client->get('photosets.getPhotos', ['photoset_id' => $url, 'page' => $page]);
                foreach ($photos->photoset->photo as $photo) {
                    FlickrDownload::dispatch($photos->photoset->owner, $photo);
                }
            }
        }

        return redirect()->route('flickr.dashboard.view')->with('success', $flashMessage);
    }

    /**
     * @param  string  $nsid
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
