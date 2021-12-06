<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\GenericTraitEntity;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityTypeRepositoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Public\GenericTrait
 */
final class GenericTest extends TestCase
{
    private ContainerBuilder $container;

    public function setup(): void
    {
        parent::setup();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);

        $storage = $this->createMock(ContentEntityStorageInterface::class);
        $entityType = $this->createMock(EntityTypeInterface::class);
        $entityType->expects($this->any())
            ->method('getKeys')
            ->will($this->returnValue([]));
        $entityTypeManager
            ->expects($this->any())
            ->method('getDefinition')
            ->with('test_entity')
            ->willReturn($entityType);
        $entityTypeManager
            ->expects($this->any())
            ->method('getStorage')
            ->with('test_entity')
            ->willReturn($storage);
        $storage->expects($this->any())
            ->method('save')
            ->with($this->isInstanceOf(GenericTraitEntity::class))
            ->willReturn(2);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\GenericTrait::saveAnd
     * @covers \dpi\DrupalEntityTraits\Traits\Generic\GenericTrait::saveResultByReference
     */
    public function testSaveAnd(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new GenericTraitEntity([], 'test_entity');
        $entity->saveAnd($result);
        $this->assertEquals(2, $result);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\GenericTrait::entityStorage
     * @covers \dpi\DrupalEntityTraits\Traits\Generic\GenericTrait::getEntityStorage
     */
    public function testEntityStorage(): void
    {
        $entityTypeRepository = $this->createMock(EntityTypeRepositoryInterface::class);
        $this->container->set('entity_type.repository', $entityTypeRepository);
        \Drupal::setContainer($this->container);

        $entityTypeRepository->expects($this->once())
            ->method('getEntityTypeFromClass')
            ->with(GenericTraitEntity::class)
            ->willReturn('test_entity');

        $entity = new GenericTraitEntity([], 'test_entity');
        $storage = $entity::entityStorage();
        $this->assertInstanceOf(ContentEntityStorageInterface::class, $storage);
    }
}
