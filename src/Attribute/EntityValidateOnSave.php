<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Attribute;

/**
 * Causes entity validation on save.
 *
 * Used in combination with SaveTrait.
 *
 * @see \dpi\DrupalEntityTraits\Traits\Generic\SaveTrait
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class EntityValidateOnSave
{
}
