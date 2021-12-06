<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Generic;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;

/**
 * Functionality that applies to all content entities.
 */
trait GenericTrait
{
    /**
     * Saves this entity and returns for chaining.
     *
     * @param int|null $result
     *   If passed, value will be updated result of the save operation
     */
    private function saveResultByReference(?int &$result = 0): void
    {
        $result = $this->save();
    }

    /**
     * Entity storage for this entity type.
     */
    private static function getEntityStorage(): ContentEntityStorageInterface
    {
        $entityTypeRepository = \Drupal::service('entity_type.repository');
        assert($entityTypeRepository instanceof EntityTypeRepositoryInterface);
        $storage = \Drupal::entityTypeManager()->getStorage($entityTypeRepository->getEntityTypeFromClass(static::class));
        assert($storage instanceof ContentEntityStorageInterface);

        return $storage;
    }
}
