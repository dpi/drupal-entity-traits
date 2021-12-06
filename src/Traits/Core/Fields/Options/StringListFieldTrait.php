<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Options;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;

trait StringListFieldTrait
{
    use BaseListFieldTrait;

    /**
     * Gets label of a list field.
     *
     * [#Field] is required.
     *
     * @return string|null
     *   The label, or NULL if no value found
     */
    protected function getLabelFromStringListField(): ?string
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        return $this->doGetLabelFromListField($drupalField->getFieldName());
    }

    /**
     * Gets key of a list field.
     *
     * [#Field] is required.
     * [#Validate] can be used to return key is allowed.
     *
     * @return string|null
     *   The key, or NULL if no value found
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value, only when [#Validate(TRUE)]
     */
    protected function getKeyFromStringListField(): ?string
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $validate = Attribute::stack(Validate::class);

        return $this->doGetKeyFromListField($drupalField->getFieldName(), $validate instanceof Validate, $validate?->throw ?? false);
    }

    /**
     * Sets value of a list field.
     *
     * [#Field] is required.
     * [#Validate] can be used to ensure key is allowed.
     *
     * @param string $key
     *   A key
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value, only when [#Validate] is present
     */
    protected function setValueToStringListField(string $key): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $validate = Attribute::stack(Validate::class) instanceof Validate;
        $this->doSetValueToListField($key, $drupalField->getFieldName(), $validate);
    }

    /**
     * Get allowed values of a list field.
     *
     * @return array<string, string>
     *   An array of labels keyed by key
     */
    protected function getAllowedValuesOfStringListField(): array
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        return $this->doGetAllowedValuesOfListField($drupalField->getFieldName());
    }
}
