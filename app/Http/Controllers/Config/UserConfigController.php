<?php

namespace App\Http\Controllers\Config;

use App\Forms\UserConfigForm;
use App\Http\Controllers\BaseController;
use App\Models\UserConfig;
use App\Repositories\CrawlerEndpoints;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Kris\LaravelFormBuilder\FormBuilder;

class UserConfigController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param  FormBuilder  $formBuilder
     * @param  UserConfig  $configModel
     *
     * @return Application|Factory|View
     */
    public function show(FormBuilder $formBuilder, UserConfig $configModel)
    {
        $this->generateMetaTags();

        $form = $formBuilder->create(
            UserConfigForm::class,
            [
                'method' => 'POST',
                'url' => route('config.user.view')
            ],
            $configModel->getAllUserConfigs()->toArray()
        );

        return view(
            'config.index',
            $this->getViewDefaultOptions(
                [
                    'endpoints' => app(CrawlerEndpoints::class)->getItems(),
                    'title' => 'Configuration',
                    'form' => $form,
                ]
            )
        );
    }

    /**
     * @param  FormBuilder  $formBuilder
     * @param  UserConfig  $userConfig
     *
     * @return RedirectResponse
     */
    public function store(FormBuilder $formBuilder, UserConfig $userConfig): RedirectResponse
    {
        $form = $formBuilder->create(UserConfigForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $userConfig->updateConfigs($form->getFieldValues());

        return redirect()->route('config.user.view')->with('success', 'Config store successfully');
    }
}
