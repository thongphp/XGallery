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

use App\Facades\FlickrClient;
use App\Http\Controllers\BaseController;
use App\Http\Requests\FlickrDownloadRequest;
use App\Jobs\Flickr\FlickrDownloadAlbum;
use App\Jobs\Flickr\FlickrDownloadContact;
use App\Jobs\Flickr\FlickrDownloadGallery;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Flickr\Url\FlickrUrlInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\Notifiable;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FlickrController
 * @package App\Http\Controllers\Flickr
 */
class FlickrController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Notifiable;

    protected ContactRepository $repository;

    /**
     * FlickrController constructor.
     *
     * @param  ContactRepository  $repository
     */
    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  Request  $request
     * @return Application|Factory|RedirectResponse|View
     */
    public function dashboard(Request $request)
    {
        if ($view = $this->validateAuthenticate()) {
            return $view;
        }

        return parent::dashboard($request);
    }

    /**
     * @param  FlickrDownloadRequest  $request
     * @return Application|Factory|RedirectResponse|View
     */
    public function download(FlickrDownloadRequest $request)
    {
        if ($view = $this->validateAuthenticate()) {
            return $view;
        }

        if (!$result = $request->getUrl()) {
            return redirect()
                ->route('flickr.dashboard.view')
                ->with('error', 'Could not detect type of URL');
        }

        $flashMessage = 'Added <span class="badge badge-primary">%d</span> photos in %s: <strong>%s</strong> / <span class="badge badge-secondary">%s</span>';

        try {
            switch ($result->getType()) {
                case FlickrUrlInterface::TYPE_ALBUM:
                    $albumInfo = FlickrClient::getPhotoSetInfo($result->getId());

                    if (!$albumInfo || $albumInfo->photoset->photos === 0) {
                        return redirect()->route('flickr.dashboard.view')
                            ->with('error', 'Can not get Album information or album has no photos.');
                    }

                    // @todo Create photoset object
                    FlickrDownloadAlbum::dispatch($albumInfo->photoset);

                    $flashMessage = sprintf(
                        $flashMessage,
                        $albumInfo->photoset->photos,
                        'album',
                        $albumInfo->photoset->title,
                        $albumInfo->photoset->id
                    );

                    break;

                case FlickrUrlInterface::TYPE_GALLERY:
                    $galleryInfo = FlickrClient::getGalleryInformation($result->getId());

                    if (!$galleryInfo || $galleryInfo->gallery->count_photos === 0) {
                        return redirect()->route('flickr.dashboard.view')
                            ->with('error', 'Can not get Gallery information or gallery has no photos.');
                    }

                    FlickrDownloadGallery::dispatch($galleryInfo->gallery);

                    $flashMessage = sprintf(
                        $flashMessage,
                        $galleryInfo->gallery->count_photos,
                        'gallery',
                        $galleryInfo->gallery->title,
                        $galleryInfo->gallery->gallery_id
                    );

                    break;

                case FlickrUrlInterface::TYPE_PROFILE:
                    FlickrDownloadContact::dispatch($result->getOwner());

                    $flashMessage = 'Added user <strong>'.$result->getOwner().'</strong>';

                    break;

                default:
                    return redirect()
                        ->route('flickr.dashboard.view')
                        ->with('error', 'Could not detect type of URL');
            }
        } catch (Exception $exception) {
            return redirect()
                ->route('flickr.dashboard.view')
                ->with('error', $exception->getMessage());
        }

        return redirect()->route('flickr.dashboard.view')->with('success', $flashMessage);
    }

    /**
     * @param  string  $nsid
     * @return Application|Factory|RedirectResponse|View
     */
    public function contact(string $nsid)
    {
        if ($view = $this->validateAuthenticate()) {
            return $view;
        }

        $items = app(ContactRepository::class)
            ->findOrCreateByNsId($nsid)
            ->refPhotos()
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
