<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity\Options;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for string list trait.
 */
final class FloatListEntity extends ContentEntityBase
{
    use FloatListFieldTrait;

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
    public function getMyKeyFromFloatListField(): ?float
    {
        return $this->getKeyFromFloatListField();
    }

    #[Field('my_field')]
    #[Validate(false)]
    public function getMyKeyFromFloatListFieldSafe(): ?float
    {
        return $this->getKeyFromFloatListField();
    }

    #[Field('my_field')]
    public function getMyLabelFromFloatListField(): ?string
    {
        return $this->getLabelFromFloatListField();
    }

    /**
     * @return $this
     *
     * @throws \dpi\DrupalEntityTraits\Exception\ValidationException
     *   Thrown if key is not an allowed value
     */
    #[Field('my_field')]
    public function setMyValueToFloatListField(float $key)
    {
        $this->setValueToFloatListField($key);

        return $this;
    }

    /**
     * @return array<string, string>
     *   An array of labels keyed by key
     */
    #[Field('my_field')]
    public function getMyAllowedValuesOfFloatListField(): array
    {
        return $this->getAllowedValuesOfFloatListField();
    }
}
