<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Generic;

use dpi\DrupalEntityTraits\Attribute\EntityValidateOnSave;
use dpi\DrupalEntityTraits\Exception\EntityValidationViolations;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Entity\EntityConstraintViolationListInterface;

trait SaveTrait
{
    /**
     * @todo Change FALSE return after https://www.drupal.org/project/drupal/issues/2509360
     *
     * @throws \Drupal\Core\Entity\EntityStorageException
     * @throws \dpi\DrupalEntityTraits\Exception\EntityValidationViolations
     */
    private function saveAndValidate(): int|false
    {
        $entityValidateOnSave = Attribute::fromClass($this::class, EntityValidateOnSave::class);
        if ($entityValidateOnSave) {
            $violations = $this->validate();
            assert($violations instanceof EntityConstraintViolationListInterface);
            0 === $violations->count() ?: throw new EntityValidationViolations($violations);
        }
        $result = parent::save();

        return $result;
    }
}
