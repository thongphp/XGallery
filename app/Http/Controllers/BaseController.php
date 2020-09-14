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
use App\Models\User;
use App\Traits\HasObject;
use Auth;
use Butschster\Head\Contracts\MetaTags\MetaInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

/**
 * Class BaseController
 * @package App\Http\Controllers
 */
class BaseController extends Controller
{
    use HasMenu;
    use HasObject;

    protected MetaInterface $meta;

    /**
     * BaseController constructor.
     *
     * @param MetaInterface $meta
     */
    public function __construct(MetaInterface $meta)
    {
        $this->meta = $meta;

        $this->generateGeneralMetaTags();
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return strtolower(str_replace('Controller', '', $this->getShortClassname()));
    }

    /**
     * @param array $options
     *
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

    private function generateGeneralMetaTags(): void
    {
        $title = config('app.name').' - '.ucfirst($this->getName());

        $this->meta->setTitle($title)
            ->addMeta('author', ['content' => env('META_AUTHOR')])
            ->includePackages(['twitter', 'opengraph']);
    }

    /**
     * @param array $twitterMeta
     * @param array $facebookMeta
     */
    protected function generateMetaTags(array $twitterMeta = [], array $facebookMeta = []): void
    {
        $title = config('app.name').' - '.ucfirst($this->getName());
        $defaultDescription = config('meta_tags.description.default');

        $twitterDefaultMeta = [
            'twitter:site' => '@soulevil',
            'twitter:creator' => '@soulevil',
            'twitter:title' => $title,
            'twitter:description' => $defaultDescription,
            'twitter:url' => URL::current(),
            'twitter:card' => 'Summary',
        ];
        $twitterMeta = array_merge($twitterDefaultMeta, $twitterMeta);

        foreach ($twitterMeta as $metaName => $metaContent) {
            $this->meta->addMeta($metaName, ['content' => $metaContent]);
        }

        $facebookDefaultMeta = [
            'og:type' => 'website',
            'og:site_name' => $title,
            'og:title' => $title,
            'og:url' => URL::current(),
            'og:description' => $defaultDescription,
            'og:image' => '',
        ];
        $facebookMeta = array_merge($facebookDefaultMeta, $facebookMeta);

        foreach ($facebookMeta as $item => $value) {
            if (!is_array($value)) {
                $this->meta->addMeta($item, ['content' => $value, 'property' => $item], false);
                continue;
            }

            foreach ($value as $key => $singleValue) {
                $this->meta->addMeta($item.$key, ['content' => $singleValue, 'property' => $item], false);
            }
        }
    }
}
