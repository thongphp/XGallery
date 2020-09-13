<?php

namespace App\Forms;

use App\Facades\FormTool;
use Kris\LaravelFormBuilder\Field;
use Kris\LaravelFormBuilder\Form;

class AdminConfigForm extends Form
{
    private const XML = 'Config/config.admin.xml';

    public function buildForm(): void
    {
        $result = FormTool::parseXML(app_path(self::XML));

        foreach ($result as $formField) {
            if (count($formField) === 1) {
                $this->processBuildField($formField);

                continue;
            }

            foreach ($formField as $field) {
                $this->processBuildField($field);
            }
        }

        $this->add('submit', Field::BUTTON_SUBMIT);
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
    private function processBuildField(array $field): void
    {
        if (!isset($field['@attributes'])) {
            return;
        }

        $fieldAttribute = $field['@attributes'];

        FormTool::buildField($fieldAttribute, $this);
    }

    /**
     * @param  array  $fieldValues
     *
     * @param  Form  $form
     * @return array
     */
    private function processBuildFieldValues(array $fieldValues, Form $form): array
    {
        return FormTool::buildFieldValues($fieldValues, $form);
    }
}
