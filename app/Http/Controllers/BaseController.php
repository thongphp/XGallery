<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Http\Controllers;

use App\Http\Traits\HasMenu;
use App\Traits\HasObject;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

/**
 * Class BaseController
 * @package App\Http\Controllers
 */
class BaseController extends Controller
{
    use HasMenu;
    use HasObject;

    /**
     * @return string
     */
    protected function getName(): string
    {
        return strtolower(str_replace('Controller', '', $this->getShortClassname()));
    }

    /**
     * @param  array  $options
     * @return array
     */
    protected function getViewDefaultOptions(array $options): array
    {
        return array_merge(
            [
                'sidebar' => $this->getMenuItems(),
                'title' => ucfirst($this->getName()),
            ],
            $options
        );
    }

    /**
     * @return Application|Factory|View|null
     */
    protected function validateAuthenticate()
    {
        $user = Auth::user();
        $flickrOAuth = $user->getOAuth('flickr');
        $googleOAuth = $user->getOAuth('google');

        if (!$flickrOAuth || !$googleOAuth) {
            return view(
                'includes.authorization',
                $this->getViewDefaultOptions(
                    ['flickr' => (bool) $flickrOAuth, 'google' => (bool) $googleOAuth, 'title' => 'Authorization']
                )
            );
        }

        return null;
    }
}
