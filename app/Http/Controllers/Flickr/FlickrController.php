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
use App\Services\Flickr\Url\FlickrUrlInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
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

        if (!$url = $request->get('url')) {
            return view('flickr.index', $this->getViewDefaultOptions([
                'title' => 'Flickr'
            ]));
        }

        $this->generateMetaTags();

        $result = UrlExtractor::extract($url);

        if (!$result) {
            return redirect()->route('flickr.dashboard.view')->with('warning', 'Could not detect type of URL');
        }

        try {
            $profile = FlickrClient::getPeopleInfo($result->getOwner());
            $message = 'URL type is <span class="badge badge-primary">%s</span>';
            switch ($result->getType()) {
                case FlickrUrlInterface::TYPE_ALBUM:
                    $message = sprintf($message, FlickrUrlInterface::TYPE_ALBUM);
                    $data = FlickrClient::getPhotoSetInfo($result->getId());
                    break;
                case FlickrUrlInterface::TYPE_GALLERY:
                    $message = sprintf($message, FlickrUrlInterface::TYPE_GALLERY);
                    $data = FlickrClient::getGalleryInformation($result->getId());
                    break;
                case FlickrUrlInterface::TYPE_PROFILE:
                    $message = sprintf($message, FlickrUrlInterface::TYPE_PROFILE);
                    $data = FlickrClient::getPeopleInfo($result->getOwner());
                    break;
                case FlickrUrlInterface::TYPE_PHOTO:
                    $message = sprintf($message, FlickrUrlInterface::TYPE_PHOTO);
                    $data = FlickrClient::getPeopleInfo($result->getOwner());
                    break;
                default:
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
     * @param FlickrDownloadRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function download(FlickrDownloadRequest $request): JsonResponse
    {
        if (!$flickrDownload = $request->getUrl()) {
            return response()->json([
                'html' => Toast::warning('Download', 'Could not detect type of URL')
            ]);
        }

        // @TODO Move to layout
        $flashMessage = 'Added <span class="badge badge-primary">%d</span> photos of %s <strong>%s</strong>';

        if (!$flickrDownload->isValid()) {
            return response()->json([
                'html' => Toast::warning(
                    'Download',
                    'Can not get '.ucfirst($flickrDownload->getType()).' information'
                )
            ]);
        }

        if ($flickrDownload->getPhotosCount() === 0) {
            return response()->json([
                'html' => Toast::warning(
                    'Download',
                    ucfirst($flickrDownload->getType()).' has no photos'
                )
            ]);
        }

        $flickrDownload->download();

        return response()->json([
            'html' => Toast::success(
                'Download',
                sprintf(
                    $flashMessage,
                    $flickrDownload->getPhotosCount(),
                    ucfirst($flickrDownload->getType()),
                    $flickrDownload->getTitle()
                )
            )
        ]);
    }
}
