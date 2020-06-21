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
use App\Jobs\Flickr\FlickrDownloadContact;
use App\Jobs\Flickr\FlickrDownloadGallery;
use App\Models\Flickr\Photo;
use App\Repositories\Flickr\ContactRepository;
use App\Repositories\OAuthRepository;
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $oAuthRepository = app(OAuthRepository::class);
        $flickrOAuth = $oAuthRepository->findBy(['name' => 'flickr']);
        $googleOAuth = $oAuthRepository->findBy(['name' => 'google']);

        if ($flickrOAuth && $googleOAuth) {
            return parent::dashboard($request);
        }

        return view(
            'flickr.authorization',
            $this->getViewDefaultOptions([
                'title' => 'Flickr',
                'flickr' => (bool) $flickrOAuth,
                'google' => (bool) $googleOAuth,
            ])
        );
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
                    return redirect()->route('flickr.dashboard.view')
                        ->with('error', 'Can not get Album information or album has no photos.');
                }

                FlickrDownloadAlbum::dispatchNow($albumInfo->photoset);

                $flashMessage = 'Add album: '.$albumInfo->photoset->title.' ('.$albumInfo->photoset->id.') successful';

                break;

            case FlickrUrlInterface::TYPE_GALLERY:
                $galleryInfo = Flickr::getGalleryInformation($result->getId());

                if (!$galleryInfo || $galleryInfo->gallery->count_photos === 0) {
                    return redirect()->route('flickr.dashboard.view')
                        ->with('error', 'Can not get Gallery information or gallery has no photos.');
                }

                FlickrDownloadGallery::dispatchNow($galleryInfo->gallery);

                $flashMessage = 'Add gallery: '.$galleryInfo->gallery->title.' ('.$galleryInfo->gallery->gallery_id.') successful';
                break;

            case FlickrUrlInterface::TYPE_PROFILE:
                FlickrDownloadContact::dispatchNow($result->getId());

                $flashMessage = 'Add user: '.$result->getId().' successful';

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
        $contact = app(ContactRepository::class)->findOrCreateByNsId($nsid);
        $items = $contact->refPhotos()->where([Photo::KEY_STATUS => true])
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
