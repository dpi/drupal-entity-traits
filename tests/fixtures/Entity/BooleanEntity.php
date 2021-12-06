<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Traits\Core\Fields\BooleanFieldTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for list trait.
 */
final class BooleanEntity extends ContentEntityBase
{
    use BooleanFieldTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    #[Field('my_field')]
    public function getMyBooleanFromBooleanField(): bool
    {
        return $this->getBooleanFromBooleanField();
    }

    /**
     * @return $this
     */
    #[Field('my_field')]
    public function setMyBooleanToBooleanField(bool $value)
    {
        $this->setBooleanToBooleanField($value);

        return $this;
    }
}
