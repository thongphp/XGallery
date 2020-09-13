<?php

namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Field;
use Kris\LaravelFormBuilder\Form;

class Checkbox implements FieldInterface
{
    /**
     * @param  array  $attributes
     * @param  Form  $form
     *
     * @return Form
     */
    public function processBuildField(array $attributes, Form $form): Form
    {
        $formValue = $form->getData($attributes['name']);
        $isChecked = $formValue ? (bool) $formValue['value'] : $attributes['defaultChecked'];

        $form->add(
            $attributes['name'],
            Field::CHECKBOX,
            [
                'label' => $attributes['label'],
                'value' => $attributes['value'],
                'description' => $attributes['description'],
                'checked' => $isChecked
            ]
        );

        return $form;
    }

    /**
     * @param  mixed  $fieldValue
     *
     * @return bool|mixed
     */
    public function processFieldValues($fieldValue)
    {
        return (bool) $fieldValue;
    }
}
