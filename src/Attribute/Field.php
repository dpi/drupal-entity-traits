<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Attribute;

/**
 * Represents a field on a content entity.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Field
{
    public function __construct(
      protected string $fieldName,
    ) {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
