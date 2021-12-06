<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity\Options;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for string list trait.
 */
final class StringListEntity extends ContentEntityBase
{
    use StringListFieldTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    /**
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value
     */
    #[Field('my_field')]
    #[Validate]
    public function getMyKeyFromStringListField(): ?string
    {
        return $this->getKeyFromStringListField();
    }

    #[Field('my_field')]
    #[Validate(false)]
    public function getMyKeyFromListFieldSafe(): ?string
    {
        return $this->getKeyFromStringListField();
    }

    #[Field('my_field')]
    public function getMyLabelFromStringListField(): ?string
    {
        return $this->getLabelFromStringListField();
    }

    /**
     * @return $this
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value
     */
    #[Field('my_field')]
    public function setMyValueToStringListField(string $key)
    {
        $this->setValueToStringListField($key);

        return $this;
    }

    /**
     * @return array<string, string>
     *   An array of labels keyed by key
     */
    #[Field('my_field')]
    public function getMyAllowedValuesOfStringListField(): array
    {
        return $this->getAllowedValuesOfStringListField();
    }
}
