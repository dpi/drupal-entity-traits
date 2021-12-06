<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Exception\EntityValidationViolations;
use dpi\DrupalEntityTraits\Tests\fixtures\Entity\SaveTraitEntity;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Public\SaveTrait
 */
final class SaveTest extends TestCase
{
    private ContainerBuilder $container;

    /**
     * @var ValidatorInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private ValidatorInterface $validator;

    public function setup(): void
    {
        parent::setup();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $typedDataManager = $this->createMock(TypedDataManagerInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);
        $this->container->set('typed_data_manager', $typedDataManager);

        $storage = $this->createMock(ContentEntityStorageInterface::class);
        $entityType = $this->createMock('\Drupal\Core\Entity\EntityTypeInterface');
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
            ->with($this->isInstanceOf(SaveTraitEntity::class))
            // \SAVED_UPDATED.
            ->willReturn(2);

        $typedDataManager->expects($this->any())
            ->method('hasDefinition')
            ->willReturn(true);
        $typedDataManager->expects($this->any())
            ->method('getDefinition')
            ->willReturn(['class' => EntityAdapter::class]);

        $this->validator = $this->createMock(ValidatorInterface::class);
        $typedDataManager->expects($this->any())
            ->method('getValidator')
            ->willReturn($this->validator);

        $fieldDefinition = $this->getMockBuilder(FieldDefinitionInterface::class)->getMock();
        $entityFieldManager->expects($this->any())
            ->method('getFieldDefinitions')
            ->willReturn([
                'foo' => $fieldDefinition,
                'bar' => $fieldDefinition,
            ]);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Generic\SaveTrait::saveAndValidate
     * @covers \dpi\DrupalEntityTraits\Traits\Public\SaveTrait::save
     */
    public function testSave(): void
    {
        \Drupal::setContainer($this->container);
        $violations = new ConstraintViolationList();
        $this->validator->expects($this->once())->method('validate')->willReturn($violations);

        $entity = new SaveTraitEntity([], 'test_entity');
        $this->assertEquals(2, $entity->save());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Generic\SaveTrait::saveAndValidate
     * @covers \dpi\DrupalEntityTraits\Traits\Public\SaveTrait::save
     */
    public function testSaveException(): void
    {
        \Drupal::setContainer($this->container);
        $violations = new ConstraintViolationList();
        $this->validator->expects($this->once())->method('validate')->willReturn($violations);
        $violations->add(new ConstraintViolation('Hello world', '', [], null, 'foo', 'bar'));
        $violations->add(new ConstraintViolation('Hello world', '', [], null, 'bar', 'bar'));

        $entity = new SaveTraitEntity([], 'test_entity');
        $this->expectException(EntityValidationViolations::class);
        $this->expectExceptionMessage('Entity has violation failures in foo, bar');
        $entity->save();
    }
}
