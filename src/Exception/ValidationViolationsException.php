<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Thrown when a field has validation violations.
 *
 * This is only called when a class has a [#Validate] attribute.
 *
 * @see \dpi\DrupalEntityTraits\Attribute\Validate
 */
class ValidationViolationsException extends ValidationException
{
    public function __construct(public ConstraintViolationListInterface $violations)
    {
        $messages = implode(', ', array_map(
            fn (ConstraintViolationInterface $violation): string => (string) $violation->getMessage(),
            iterator_to_array($this->violations),
        ));
        parent::__construct(sprintf('Violation failures found: %s', $messages));
    }
}
