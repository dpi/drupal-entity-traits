<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\DateEntity;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\Plugin\DataType\DateTimeIso8601;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\datetime\DateTimeComputed;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeFieldItemList;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateTrait
 */
final class DateTest extends TestCase
{
    private ContainerBuilder $container;
    private string $testValue = '2014-06-14T01:00:00';

    public function setup(): void
    {
        parent::setup();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $fieldTypePluginManager = $this->createMock(FieldTypePluginManagerInterface::class);
        $typedDataManager = $this->createMock(TypedDataManagerInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);
        $this->container->set('plugin.manager.field.field_type', $fieldTypePluginManager);
        $this->container->set('typed_data_manager', $typedDataManager);
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
            ->with('datetime_entity')
            ->willReturn($entityType);

        $definition = $this->createMock(ComplexDataDefinitionInterface::class);
        $definition->expects($this->any())
            ->method('getPropertyDefinitions')
            ->willReturn([
                'value' => $this->createMock(DataDefinitionInterface::class),
            ]);
        $definition->expects($this->any())
            ->method('getSetting')
            ->with('date source')
            ->willReturn('value');

        $value = new DateTimeIso8601($definition);
        $value->setValue($this->testValue);
        $ddt = $this->createMock(DrupalDateTime::class);
        $ddt->expects($this->any())
            ->method('getPhpDateTime')
            // Drupal's DDT always use UTC.
            ->willReturn(new \DateTime('14th June 2014 1am', new \DateTimeZone('UTC')));
        $date = $this->createMock(DateTimeComputed::class);
        $date->expects($this->any())
            ->method('getValue')
            ->willReturn($ddt);

        $typedDataManager->expects($this->any())
            ->method('getPropertyInstance')
            ->withConsecutive(
                [$this->isInstanceOf(DateTimeItem::class), 'value', $this->testValue],
                [$this->isInstanceOf(DateTimeItem::class), 'date', null]
            )
            ->willReturnOnConsecutiveCalls(
                $value,
                $date,
            );

        $list = new DateTimeFieldItemList($definition);
        $item = new DateTimeItem($definition);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->with($this->isInstanceOf(DateEntity::class), 'my_field', null)
            ->will($this->returnValue($list));
        $fieldTypePluginManager->expects($this->once())
            ->method('createFieldItem')
            ->with($this->isInstanceOf(DateTimeFieldItemList::class), 0, $this->testValue)
            ->willReturn($item);

        $item->set('value', $this->testValue);
        // @phpstan-ignore-next-line
        $list->setValue($this->testValue, false);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateTrait::getDateTimeFromDateTimeField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateTrait::doGetDateTimeFromDateTimeField
     */
    public function testGetTimestampAsDateTime(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new DateEntity([], 'datetime_entity');
        $return = $entity->getMyDateFieldAsDateTime();
        $this->assertInstanceOf(\DateTimeImmutable::class, $return);
        $this->assertEquals('Sat, 14 Jun 2014 09:00:00 +0800', $return->format('r'));
        $this->assertEquals('Asia/Singapore', $return->getTimezone()->getName());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateTrait::setDateTimeToDateTimeField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateTrait::doSetDateTimeToDateTimeField
     */
    public function testSetTimeZoneForTimestamp(): void
    {
        \Drupal::setContainer($this->container);
        $date = new \DateTime('14th June 2014 9am');
        $entity = new DateEntity([], 'datetime_entity');
        $entity->setMyDateTimeToDateField($date);
    }
}
