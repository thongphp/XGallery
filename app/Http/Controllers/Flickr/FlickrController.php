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

use App\Exceptions\Flickr\FlickrApiPeopleGetInfoUserDeletedException;
use App\Facades\Flickr\UrlExtractor;
use App\Facades\FlickrClient;
use App\Http\Controllers\BaseController;
use App\Http\Helpers\Toast;
use App\Http\Requests\FlickrDownloadRequest;
use App\Jobs\Flickr\FlickrDownloadContact;
use App\Jobs\Flickr\FlickrDownloadGallery;
use App\Notifications\FlickrRequestDownload;
use App\Notifications\FlickrRequestException;
use App\Repositories\Flickr\ContactRepository;
use App\Services\Flickr\Objects\FlickrAlbum;
use App\Services\Flickr\Url\FlickrUrlInterface;
use App\Traits\Notifications\HasSlackNotification;
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
    use Notifiable, HasSlackNotification;

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
     *
     * @return Application|Factory|RedirectResponse|View
     */
    public function dashboard(Request $request)
    {
        if ($view = $this->validateAuthenticate()) {
            return $view;
        }

        if (!$url = $request->get('url')) {
            return parent::dashboard($request);
        }

        $result = UrlExtractor::extract($url);
        try {
            $profile = FlickrClient::getPeopleInfo($result->getOwner());
            $message = 'URL type is <span class="badge badge-primary">%s</span>';
            switch ($result->getType()) {
                case FlickrUrlInterface::TYPE_ALBUM:
                    $message = sprintf($message, FlickrUrlInterface::TYPE_ALBUM);
                    $data = FlickrClient::getPhotoSetInfo($result->getId());
                    break;
            }
        } catch (FlickrApiPeopleGetInfoUserDeletedException $exception) {
            return redirect()->route('flickr.dashboard.view')->with('warning', 'User has been deleted');
        }

        return view(
            $this->getName().'.index',
            $this->getViewDefaultOptions([
                'profile' => $profile ?? null,
                'message' => $message,
                'type' => $result->getType(),
                'data' => $data ?? null,
            ])
        );
    }

    /**
     * @param  FlickrDownloadRequest  $request
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
                    // @todo Actually it should be model instead
                    $album = new FlickrAlbum($result->getId());
                    if (!$album->load()) {
                        return response()->json([
                            'html' => Toast::warning(
                                'Download',
                                'Can not get Album information'
                            )
                        ]);
                    }

                    if ($album->getPhotosCount() === 0) {
                        return response()->json([
                            'html' => Toast::warning(
                                'Download',
                                'Album has no photos'
                            )
                        ]);
                    }

                    $album->download();

                    $flashMessage = sprintf(
                        $flashMessage,
                        $album->getPhotosCount(),
                        'album',
                        $album->getTitle()
                    );

                    break;

                case FlickrUrlInterface::TYPE_GALLERY:
                    $galleryInfo = FlickrClient::getGalleryInformation($result->getId());

                    if (!$galleryInfo || $galleryInfo->gallery->count_photos === 0) {
                        return response()->json([
                            'html' => Toast::warning(
                                'Download',
                                'Can not get Gallery information or gallery has no photos'
                            )
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
