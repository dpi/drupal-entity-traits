<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\EntityValidateOnSave;
use dpi\DrupalEntityTraits\Traits\Public\SaveTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for save trait.
 */
#[EntityValidateOnSave]
final class SaveTraitEntity extends ContentEntityBase
{
    use SaveTrait;

    public function bundle()
    {
        return 'testbundle';
    }
}
