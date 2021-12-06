<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Options;

use dpi\DrupalEntityTraits\Exception\ValidationException;
use dpi\DrupalEntityTraits\Exception\ValidationViolationsException;
use Drupal\Core\Field\FieldItemList;
use Drupal\options\Plugin\Field\FieldType\ListItemBase;

trait BaseListFieldTrait
{
    /**
     * Gets label of a list field.
     *
     * @param string $fieldName
     *   A field name
     *
     * @return string|null
     *   The label, or NULL if no value found
     *
     * @internal
     */
    protected function doGetLabelFromListField(string $fieldName): ?string
    {
        $key = $this->doGetKeyFromListField($fieldName, true, false);

        return $this->doGetAllowedValuesOfListField($fieldName)[$key] ?? null;
    }

    /**
     * Gets key of a list field.
     *
     * @param string $fieldName
     *   A field name
     * @param bool $validate
     *   Validate list key. If key is invalid then NULL is returned.
     * @param bool $throw
     *   Whether to throw an exception or return NULL on if $validate is TRUE
     *
     * @return string|null
     *   The key, or NULL if no valid found. May also return NULL if validating
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value, only when $validate and $throw are TRUE
     *
     * @internal
     */
    protected function doGetKeyFromListField(string $fieldName, bool $validate, bool $throw): ?string
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof FieldItemList);
        $item = $fieldList->get(0);
        assert($item instanceof ListItemBase);
        /** @var string|null $value */
        // @phpstan-ignore-next-line
        $value = $item->value ?? null;
        if (null !== $value && $validate) {
            // Use non-strict mode for in_array in case PHP auto casts key values.
            if (!in_array($value, array_keys($this->doGetAllowedValuesOfListField($fieldName)), false)) {
                !$throw ?: throw new ValidationException();

                return null;
            }
        }

        return $value;
    }

    /**
     * Sets value of a list field.
     *
     * @param string $key
     *   A key
     * @param string $fieldName
     *   A field name
     * @param bool $validate
     *   Validate list key
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value, only when $validate is TRUE
     *
     * @internal
     */
    private function doSetValueToListField(string $key, string $fieldName, bool $validate): void
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof FieldItemList);
        // @phpstan-ignore-next-line
        $fieldList->setValue($key);
        if ($validate) {
            $violations = $fieldList->validate();
            0 === $violations->count() ?: throw new ValidationViolationsException($violations);
        }
    }

    /**
     * Get allowed values of a list field.
     *
     * @param string $fieldName
     *   A field name
     *
     * @return array<string, string>
     *   An array of labels keyed by machine name
     *
     * @internal
     */
    private function doGetAllowedValuesOfListField(string $fieldName): array
    {
        /** @var array<string, string>|null $allowedValues */
        $allowedValues = $this
            ->getFieldDefinition($fieldName)
            ?->getFieldStorageDefinition()
            ?->getSetting('allowed_values');

        return $allowedValues ?? [];
    }
}
