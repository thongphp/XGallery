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
use App\Http\Helpers\Toast;
use App\Http\Requests\FlickrDownloadRequest;
use App\Jobs\Flickr\FlickrDownloadAlbum;
use App\Jobs\Flickr\FlickrDownloadContact;
use App\Jobs\Flickr\FlickrDownloadGallery;
use App\Models\Flickr\FlickrPhotoModel;
use App\Notifications\FlickrRequestDownload;
use App\Notifications\FlickrRequestException;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Flickr\Url\FlickrUrlInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
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
     * @param ContactRepository $repository
     */
    public function __construct(ContactRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     *
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
     * @param FlickrDownloadRequest $request
     *
     * @return JsonResponse
     */
    public function download(FlickrDownloadRequest $request): JsonResponse
    {
        if (!$result = $request->getUrl()) {
            return response()->json([
                'html' => Toast::warning('Download', 'Could not detect type of URL')
            ]);
        }

        $flashMessage = 'Added <span class="badge badge-primary">%d</span> photos of %s <strong>%s</strong>';
        $this->notify(new FlickrRequestDownload($result));

        try {
            switch ($result->getType()) {
                case FlickrUrlInterface::TYPE_ALBUM:
                    $albumInfo = FlickrClient::getPhotoSetInfo($result->getId());

                    if ($albumInfo->photoset->photos === 0) {
                        return response()->json([
                            'html' => Toast::warning('Download', 'Can not get Album information or album has no photos')
                        ]);
                    }

                    // @todo Create photoset object
                    FlickrDownloadAlbum::dispatch($albumInfo);

                    $flashMessage = sprintf(
                        $flashMessage,
                        $albumInfo->photoset->photos,
                        'album',
                        $albumInfo->photoset->title
                    );

                    break;

                case FlickrUrlInterface::TYPE_GALLERY:
                    $galleryInfo = FlickrClient::getGalleryInformation($result->getId());

                    if (!$galleryInfo || $galleryInfo->gallery->count_photos === 0) {
                        return response()->json([
                            'html' => Toast::warning('Download',
                                'Can not get Gallery information or gallery has no photos')
                        ]);
                    }

                    FlickrDownloadGallery::dispatch($galleryInfo->gallery);

                    $flashMessage = sprintf(
                        $flashMessage,
                        $galleryInfo->gallery->count_photos,
                        'gallery',
                        $galleryInfo->gallery->title
                    );

                    break;

                case FlickrUrlInterface::TYPE_PROFILE:
                    FlickrDownloadContact::dispatch($result->getOwner());

                    $flashMessage = 'Added download all photos of user <strong>'.$result->getOwner().'</strong>';

                    break;
                default:
                    throw new Exception();
                    break;
            }
        } catch (Exception $exception) {
            $this->notify(new FlickrRequestException($exception->getMessage(), $request->get('url')));

            return response()->json([
                'html' => Toast::warning('Download', $exception->getMessage())
            ]);
        }

        return response()->json([
            'html' => Toast::success('Download', $flashMessage)
        ]);
    }
}
