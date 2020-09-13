<?php

namespace App\Http\Controllers\Config;

use App\Forms\AdminConfigForm;
use App\Http\Controllers\BaseController;
use App\Models\Config;
use App\Repositories\CrawlerEndpoints;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Kris\LaravelFormBuilder\FormBuilder;

class ConfigController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param  FormBuilder  $formBuilder
     * @param  Config  $configModel
     *
     * @return Application|Factory|View
     */
    public function show(FormBuilder $formBuilder, Config $configModel)
    {
        $this->generateMetaTags();

        $form = $formBuilder->create(
            AdminConfigForm::class,
            [
                'method' => 'POST',
                'url' => route('config.form.view')
            ],
            $configModel->getConfigs()->toArray()
        );

        return view(
            'config.form',
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
     * @param  Config  $configModel
     *
     * @return RedirectResponse
     */
    public function store(FormBuilder $formBuilder, Config $configModel): RedirectResponse
    {
        $form = $formBuilder->create(AdminConfigForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $configModel->updateConfigs($form->getFieldValues());

        return redirect()->route('config.form.view')->with('success', 'Config store successfully');
    }
}
