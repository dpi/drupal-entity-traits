<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Attribute;

/**
 * @see \dpi\DrupalEntityTraits\Exception\ValidationViolationsException
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Validate
{
    /**
     * @param bool $throw
     *   Prefer to throw exceptions if possible when used with a getter, otherwise NULL
     *   is usually returned. Setters will always return an exception.
     */
    public function __construct(public bool $throw = true)
    {
    }
}
