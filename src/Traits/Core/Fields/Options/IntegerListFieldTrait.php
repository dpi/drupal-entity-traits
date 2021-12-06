<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields\Options;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;

trait IntegerListFieldTrait
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
    protected function getLabelFromIntegerListField(): ?string
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
     * @return int|null
     *   The key, or NULL if no value found
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value, only when [#Validate(TRUE)]
     */
    protected function getKeyFromIntegerListField(): ?int
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $validate = Attribute::stack(Validate::class);

        $value = $this->doGetKeyFromListField($drupalField->getFieldName(), $validate instanceof Validate, $validate?->throw ?? false);

        return null !== $value ? (int) $value : null;
    }

    /**
     * Sets value of a list field.
     *
     * [#Field] is required.
     * [#Validate] can be used to ensure key is allowed.
     *
     * @param int $key
     *   A key
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value, only when [#Validate] is present
     */
    protected function setValueToIntegerListField(int $key): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);
        $validate = Attribute::stack(Validate::class) instanceof Validate;
        $this->doSetValueToListField((string) $key, $drupalField->getFieldName(), $validate);
    }

    /**
     * Get allowed values of a list field.
     *
     * @return array<int, string>
     *   An array of labels keyed by key
     */
    protected function getAllowedValuesOfIntegerListField(): array
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        $values = $this->doGetAllowedValuesOfListField($drupalField->getFieldName());

        return array_combine(
            array_map(fn (string $key) => (int) $key, array_keys($values)),
            $values,
        );
    }
}
