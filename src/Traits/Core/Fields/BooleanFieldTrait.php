<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\Plugin\Field\FieldType\BooleanItem;

trait BooleanFieldTrait
{
    /**
     * Transforms value of a boolean field into a boolean.
     *
     * @return bool
     *   A boolean. If field is empty, FALSE will be returned.
     */
    protected function getBooleanFromBooleanField(): bool
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        return $this->doGetBooleanFromBooleanField($drupalField->getFieldName());
    }

    /**
     * Transforms value of a boolean field into a boolean.
     *
     * @param string $fieldName
     *   A field name
     *
     * @return bool
     *   A boolean. If field is empty, FALSE will be returned.
     *
     * @internal
     */
    private function doGetBooleanFromBooleanField(string $fieldName): bool
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof FieldItemList);
        $item = $fieldList->get(0);
        assert($item instanceof BooleanItem);
        // This will either be a numeric string '0' or '1' or NULL.
        /** @var string|null $value */
        // @phpstan-ignore-next-line
        $value = $item->value ?? null;

        return !empty($value);
    }

    /**
     * Transforms a boolean to a boolean field.
     *
     * @param bool $value
     *   A boolean
     */
    protected function setBooleanToBooleanField(bool $value): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        $this->doSetBooleanToBooleanField($value, $drupalField->getFieldName());
    }

    /**
     * Transforms a boolean to a boolean field.
     *
     * @param string $fieldName
     *   A field name
     * @param bool $value
     *   A boolean
     *
     * @internal
     */
    private function doSetBooleanToBooleanField(bool $value, string $fieldName): void
    {
        $this->{$fieldName} = $value;
    }
}
