<?php

namespace App\Services;

use App\Forms\Fields\Checkbox;
use App\Forms\Fields\FieldInterface;
use Kris\LaravelFormBuilder\Form;

class FormTool
{
    public const MAPPING = [
        self::TYPE_CHECKBOX => Checkbox::class,
    ];

    public const TYPE_CHECKBOX = 'checkbox';

    /**
     * @param  string  $xmlPath
     *
     * @return array
     * @throws \JsonException
     */
    public function parseXML(string $xmlPath): array
    {
        $formXML = simplexml_load_string(file_get_contents($xmlPath));

        return json_decode(
            json_encode((array) $formXML, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param  array  $field
     * @param  Form  $form
     *
     * @return Form
     */
    public function buildField(array $field, Form $form): Form
    {
        if (empty($field['@attributes'])
            || !isset($field['@attributes']['type'])
            || in_array($field['@attributes']['type'], self::MAPPING, true)
        ) {
            return $form;
        }

        $fieldInstance = app(self::MAPPING[$field['@attributes']['type']]);

        $form = $fieldInstance->processBuildField($field, $form);

        return $form;
    }

    /**
     * @param  array  $fieldValues
     * @param  Form  $form
     *
     * @return array
     */
    public function buildFieldValues(array $fieldValues, Form $form): array
    {
        foreach ($form->getFields() as $formField) {
            /** @var FieldInterface $field */
            $field = $this->getFieldByMapping($formField->getType());

            if (!$field || !array_key_exists($formField->getName(), $fieldValues)) {
                continue;
            }

            $fieldName = $formField->getName();

            $fieldValues[$fieldName] = $field->processFieldValues($fieldValues[$fieldName]);
        }

        return $fieldValues;
    }

    /**
     * @param  string  $fieldType
     *
     * @return FieldInterface|null
     */
    public function getFieldByMapping(string $fieldType): ?FieldInterface
    {
        if (!isset(self::MAPPING[$fieldType])) {
            return null;
        }

        return app(self::MAPPING[$fieldType]);
    }
}
