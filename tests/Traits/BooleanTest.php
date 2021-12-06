<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\BooleanEntity;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\BooleanItem;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\BooleanFieldTrait
 */
final class BooleanTest extends TestCase
{
    private ContainerBuilder $container;
    private BooleanItem $item;

    /**
     * @var \Drupal\Core\Field\FieldItemList|\PHPUnit\Framework\MockObject\MockObject
     */
    private mixed $list;

    public function setUp(): void
    {
        parent::setUp();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $fieldTypePluginManager = $this->createMock(FieldTypePluginManagerInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);
        $this->container->set('plugin.manager.field.field_type', $fieldTypePluginManager);

        $fieldStorageDefinition = $this->createMock(FieldStorageDefinitionInterface::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinitionInterface::class)->getMock();
        $fieldDefinition->expects($this->any())
            ->method('getFieldStorageDefinition')
            ->willReturn($fieldStorageDefinition);
        $entityFieldManager->expects($this->any())
            ->method('getFieldDefinitions')
            ->willReturn(['my_field' => $fieldDefinition]);

        $entityType = $this->createMock('\Drupal\Core\Entity\EntityTypeInterface');
        $entityType->expects($this->any())
            ->method('getKeys')
            ->will($this->returnValue([]));
        $entityTypeManager
            ->expects($this->any())
            ->method('getDefinition')
            ->with('test_entity')
            ->willReturn($entityType);

        $this->list = $this->createMock(FieldItemList::class);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->will($this->returnValue($this->list));
        $definition = $this->createMock(ComplexDataDefinitionInterface::class);
        $definition->expects($this->any())
            ->method('getPropertyDefinitions')
            ->willReturn([
                'value' => $this->createMock(DataDefinitionInterface::class),
            ]);

        $this->item = new BooleanItem($definition);
        $this->list->expects($this->any())
            ->method('get')
            ->with(0)
            ->willReturn($this->item);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\BooleanFieldTrait::getBooleanFromBooleanField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\BooleanFieldTrait::doGetBooleanFromBooleanField
     *
     * @dataProvider providerGetBooleanFromBooleanField
     */
    public function testGetBooleanFromBooleanField(mixed $value, bool $assertValue): void
    {
        NULL === $value ?: $this->item->set('value', $value);
        \Drupal::setContainer($this->container);
        $entity = new BooleanEntity([], 'test_entity');
        $this->assertEquals($assertValue, $entity->getMyBooleanFromBooleanField());
    }

    public function providerGetBooleanFromBooleanField(): array
    {
        return [
            'true' => ['1', true],
            'false' => ['0', false],
            'undefined' => [null, false],
        ];
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\BooleanFieldTrait::setBooleanToBooleanField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\BooleanFieldTrait::doSetBooleanToBooleanField
     */
    public function testSetBooleanToBooleanField(): void
    {
        $this->list->expects($this->once())
            ->method('setValue')
            ->with(true);

        \Drupal::setContainer($this->container);
        $entity = new BooleanEntity([], 'test_entity');
        $entity->setMyBooleanToBooleanField(true);
    }
}
