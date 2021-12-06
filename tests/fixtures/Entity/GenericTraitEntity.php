<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Traits\Public\GenericTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for generic trait.
 */
final class GenericTraitEntity extends ContentEntityBase
{
    use GenericTrait;

    public function bundle()
    {
        return 'testbundle';
    }
}
