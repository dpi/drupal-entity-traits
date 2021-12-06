<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\DateTimestampEntity;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\ChangedFieldItemList;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\ChangedItem;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\TimestampTrait
 */
final class DateTimestampTest extends TestCase
{
    private ContainerBuilder $container;
    private string $testValue = '1402707600';

    public function setup(): void
    {
        parent::setup();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $fieldTypePluginManager = $this->createMock(FieldTypePluginManagerInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);
        $this->container->set('plugin.manager.field.field_type', $fieldTypePluginManager);
        \Drupal::setContainer($this->container);

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
            ->with('timestamp_entity')
            ->willReturn($entityType);

        $definition = $this->createMock(ComplexDataDefinitionInterface::class);
        $definition->expects($this->any())
            ->method('getPropertyDefinitions')
            ->willReturn([
                'value' => $this->createMock(DataDefinitionInterface::class),
            ]);
        $list = new ChangedFieldItemList($definition);
        $item = new ChangedItem($definition);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->with($this->isInstanceOf(DateTimestampEntity::class), 'my_field', null)
            ->will($this->returnValue($list));
        $fieldTypePluginManager->expects($this->once())
            ->method('createFieldItem')
            ->with($this->isInstanceOf(ChangedFieldItemList::class), 0, $this->testValue)
            ->willReturn($item);

        $item->set('value', $this->testValue);
        // @phpstan-ignore-next-line
        $list->setValue($this->testValue, false);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\TimestampTrait::getDateTimeFromTimestampField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\TimestampTrait::doGetDateTimeFromTimestampField
     */
    public function testGetTimestampAsDateTime(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new DateTimestampEntity([], 'timestamp_entity');
        $return = $entity->getMyTimestampAsDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $return);
        $this->assertEquals('Sat, 14 Jun 2014 09:00:00 +0800', $return->format('r'));
        $this->assertEquals('Asia/Singapore', $return->getTimezone()->getName());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\TimestampTrait::setTimeZoneForTimestamp
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\TimestampTrait::doSetDateTimeToTimestampField
     */
    public function testSetTimeZoneForTimestamp(): void
    {
        \Drupal::setContainer($this->container);
        $date = new \DateTime('14th June 2014 9am');
        $entity = new DateTimestampEntity([], 'timestamp_entity');
        $entity->setMyDateTimeToTimestampField($date);
    }
}
