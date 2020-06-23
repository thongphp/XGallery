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
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseController
 * @package App\Http\Controllers
 */
class BaseController extends Controller
{
    use HasMenu;
    use HasObject;

    public function dashboard(Request $request)
    {
        $items = $this->repository->getItems($request->request->all());
        return view(
            $this->getName().'.index',
            $this->getViewDefaultOptions([
                'items' => $items,
                'title' => ucfirst($this->getName()),
            ])
        );
    }

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
            ],
            $options
        );
    }

    /**
     * @return Repository|Application|mixed
     */
    public function routeNotificationForSlack()
    {
        return config('logging.channels.slack.url');
    }
}
