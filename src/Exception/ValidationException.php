<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Exception;

/**
 * Thrown when a field has validation errors.
 *
 * This is only called when a class has a [#Validate] attribute.
 *
 * @see \dpi\DrupalEntityTraits\Attribute\Validate
 */
class ValidationException extends \Exception
{
}
