<?php

namespace App\Forms;

use App\Facades\FormTool;
use Kris\LaravelFormBuilder\Form;

class BaseForm extends Form
{
    public const XML = '';

    public function buildForm(): void
    {
        $result = FormTool::parseXML(app_path(static::XML));

        $this->setFormOptions(
            [
                'class' => 'form form-horizontal',
                'template' => 'form.form',
            ]
        );

        foreach ($result as $formField) {
            if (isset($formField['@attributes'])) {
                $this->processBuildField($formField);

                continue;
            }

            foreach ($formField as $field) {
                $this->processBuildField($field);
            }
        }
    }

    /**
     * @param  bool  $with_nulls
     *
     * @return array
     */
    public function getFieldValues($with_nulls = true)
    {
        $fieldValues = parent::getFieldValues($with_nulls);

        return $this->processBuildFieldValues($fieldValues, $this);
    }

    /**
     * @param  array  $field
     */
    public function processBuildField(array $field): void
    {
        FormTool::buildField($field, $this);
    }

    /**
     * @param  array  $fieldValues
     *
     * @param  Form  $form
     * @return array
     */
    public function processBuildFieldValues(array $fieldValues, Form $form): array
    {
        return FormTool::buildFieldValues($fieldValues, $form);
    }
}
