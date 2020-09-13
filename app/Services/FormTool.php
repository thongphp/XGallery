<?php

namespace App\Services;

use App\Forms\Fields\Checkbox;
use App\Forms\Fields\FieldInterface;
use Kris\LaravelFormBuilder\Form;

class FormTool
{
    public const MAPPING = [
        self::TYPE_CHECKBOX => Checkbox::class
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
        $object = simplexml_load_string(file_get_contents($xmlPath));

        return json_decode(
            json_encode((array) $object, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param  array  $attributes
     * @param  Form  $form
     *
     * @return Form
     */
    public function buildField(array $attributes, Form $form): Form
    {
        if (empty($attributes)
            || !isset($attributes['type'])
            || in_array($attributes['type'], self::MAPPING, true)
        ) {
            return $form;
        }

        /** @var FieldInterface $field */
        $field = app(self::MAPPING[$attributes['type']]);

        $form = $field->processBuildField($attributes, $form);

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
