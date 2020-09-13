<?php

namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Field;
use Kris\LaravelFormBuilder\Form;

class Checkbox implements FieldInterface
{
    /**
     * @param  array  $field
     * @param  Form  $form
     *
     * @return Form
     */
    public function processBuildField(array $field, Form $form): Form
    {
        $attributes = $field['@attributes'];
        $formValue = $form->getData($attributes['name']);
        $isChecked = $formValue ? (bool) $formValue['value'] : $attributes['defaultChecked'];

        $fieldOptions = [
            'wrapper' => ['class' => 'form-group'],
            'attr' => ['class' => 'form-check-input'],
            'help_block' => [
                'text' => $attributes['help'] ?? null,
                'tag' => 'div',
                'attr' => ['class' => 'help-block']
            ],
            'default_value' => null,
            'label' => $attributes['label'],
            'label_show' => true,
            'label_attr' => ['class' => 'form-check-label text-bold', 'for' => $attributes['name']],
            'errors' => ['class' => 'text-danger'],
            'rules' => [],
            'error_messages' => [],
            'template' => 'form.fields.checkbox',
            'checked' => $isChecked,
        ];

        $form->add(
            $attributes['name'],
            Field::CHECKBOX,
            $fieldOptions
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
