<?php

namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Form;

interface FieldInterface
{
    /**
     * @param  array  $attributes
     * @param  Form  $form
     * @return Form
     */
    public function processBuildField(array $attributes, Form $form): Form;

    /**
     * @param  mixed  $fieldValues
     * @return mixed
     */
    public function processFieldValues($fieldValues);
}
