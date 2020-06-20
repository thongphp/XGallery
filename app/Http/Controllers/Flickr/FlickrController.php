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
use App\Jobs\Flickr\FlickrDownloadAlbum;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\ContactRepository;
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

    protected ContactRepository $repository;

    /**
     * FlickrController constructor.
     *
     * @param ContactRepository $repository
     */
    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
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
                $albumInfo = Flickr::getAlbumInfo($result->getId());

                if (!$albumInfo || $albumInfo->photoset->photos === 0) {
                    return redirect()->route('flickr.dashboard.view')->with('error', 'Can not get photosets');
                }

                FlickrDownloadAlbum::dispatchNow($albumInfo->photoset);

                $flashMessage = 'Add album: '.$albumInfo->photoset->title.' ('.$albumInfo->photoset->id.') successfull';

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
     * @param string $nsid
     *
     * @return Application|Factory|View
     */
    public function contact(string $nsid)
    {
        $contact = app(ContactRepository::class)->getContactByNsid($nsid);
        $items = $contact->photos()->where([Photo::KEY_STATUS => true])
            ->paginate(30);

        return view(
            'flickr.photos',
            $this->getViewDefaultOptions([
                'items' => $items,
                'title' => 'Flickr',
            ])
        );
    }
}
