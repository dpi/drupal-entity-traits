<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Exception;

use Drupal\Core\Entity\EntityConstraintViolationListInterface;

/**
 * Thrown when an entity has validation violations.
 *
 * This is only called when a class has a [#EntityValidateOnSave] attribute
 * in combination with SaveTrait.
 *
 * @see \dpi\DrupalEntityTraits\Attribute\EntityValidateOnSave
 */
class EntityValidationViolations extends \Exception
{
    public function __construct(public EntityConstraintViolationListInterface $violations)
    {
        $fieldNames = implode(', ', $this->violations->getFieldNames());
        parent::__construct(sprintf('Entity has violation failures in %s', $fieldNames));
    }
}
