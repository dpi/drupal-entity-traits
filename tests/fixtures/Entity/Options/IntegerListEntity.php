<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity\Options;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for string list trait.
 */
final class IntegerListEntity extends ContentEntityBase
{
    use IntegerListFieldTrait;

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
    public function getMyKeyFromIntegerListField(): ?int
    {
        return $this->getKeyFromIntegerListField();
    }

    #[Field('my_field')]
    #[Validate(false)]
    public function getMyKeyFromIntegerListFieldSafe(): ?int
    {
        return $this->getKeyFromIntegerListField();
    }

    #[Field('my_field')]
    public function getMyLabelFromIntegerListField(): ?string
    {
        return $this->getLabelFromIntegerListField();
    }

    /**
     * @return $this
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value
     */
    #[Field('my_field')]
    public function setMyValueToIntegerListField(int $key)
    {
        $this->setValueToIntegerListField($key);

        return $this;
    }

    /**
     * @return array<int, string>
     *   An array of labels keyed by key
     */
    #[Field('my_field')]
    public function getMyAllowedValuesOfIntegerListField(): array
    {
        return $this->getAllowedValuesOfIntegerListField();
    }
}
