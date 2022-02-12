<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\DateRangeEntity;
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
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeFieldItemList;
use Drupal\datetime_range\Plugin\Field\FieldType\DateRangeItem;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRangeTrait
 */
final class DateRangeTest extends TestCase
{
    private ContainerBuilder $container;
    private string $testValue = '2014-06-14T01:00:00';
    private string $testEndValue = '2014-06-14T04:00:00';

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
        $value2 = new DateTimeIso8601($definition);
        $value2->setValue($this->testEndValue);
        $ddt = $this->createMock(DrupalDateTime::class);
        $ddt->expects($this->any())
            ->method('getPhpDateTime')
            // Drupal's DDT always use UTC.
            ->willReturn(new \DateTime('14th June 2014 1am', new \DateTimeZone('UTC')));
        $ddt2 = $this->createMock(DrupalDateTime::class);
        $ddt2->expects($this->any())
            ->method('getPhpDateTime')
            // Drupal's DDT always use UTC.
            ->willReturn(new \DateTime('14th June 2014 4am', new \DateTimeZone('UTC')));
        $date = $this->createMock(DateTimeComputed::class);
        $date->expects($this->any())
            ->method('getValue')
            ->willReturn($ddt);
        $date2 = $this->createMock(DateTimeComputed::class);
        $date2->expects($this->any())
            ->method('getValue')
            ->willReturn($ddt2);

        $typedDataManager->expects($this->any())
            ->method('getPropertyInstance')
            ->withConsecutive(
                [$this->isInstanceOf(DateRangeItem::class), 'value', $this->testValue],
                [$this->isInstanceOf(DateRangeItem::class), 'end_value', $this->testEndValue],
                [$this->isInstanceOf(DateRangeItem::class), 'start_date', null],
                [$this->isInstanceOf(DateRangeItem::class), 'end_date', null],
            )
            ->willReturnOnConsecutiveCalls(
                $value,
                $value2,
                $date,
                $date2,
            );

        $list = new DateRangeFieldItemList($definition);
        $item = new DateRangeItem($definition);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->with($this->isInstanceOf(DateRangeEntity::class), 'my_field', null)
            ->will($this->returnValue($list));
        $fieldTypePluginManager->expects($this->once())
            ->method('createFieldItem')
            ->with($this->isInstanceOf(DateRangeFieldItemList::class), 0, $this->testValue)
            ->willReturn($item);

        $item->set('value', $this->testValue);
        $item->set('end_value', $this->testEndValue);
        // @phpstan-ignore-next-line
        $list->setValue($this->testValue, false);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRangeTrait::getDateTimeFromDateRangeField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRangeTrait::doGetDateTimeFromDateRangeField
     */
    public function testGetTimestampAsDateTime(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new DateRangeEntity([], 'datetime_entity');
        $return = $entity->getMyDateRangeFieldAsDateTime();
        $this->assertIsArray($return);
        $this->assertCount(2, $return);
        [$startDate, $endDate] = $return;
        $this->assertInstanceOf(\DateTimeImmutable::class, $startDate);
        $this->assertInstanceOf(\DateTimeImmutable::class, $endDate);
        $this->assertEquals('Sat, 14 Jun 2014 09:00:00 +0800', $startDate->format('r'));
        $this->assertEquals('Sat, 14 Jun 2014 12:00:00 +0800', $endDate->format('r'));
        $this->assertEquals('Asia/Singapore', $startDate->getTimezone()->getName());
        $this->assertEquals('Asia/Singapore', $endDate->getTimezone()->getName());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRangeTrait::setDateTimeToDateRangeField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRangeTrait::doSetDateTimeToDateRangeField
     */
    public function testSetTimeZoneForTimestamp(): void
    {
        \Drupal::setContainer($this->container);
        $startDate = new \DateTimeImmutable('14th June 2014 9am', new \DateTimeZone('Asia/Singapore'));
        $endDate = new \DateTimeImmutable('14th June 2014 12pm', new \DateTimeZone('Asia/Singapore'));
        $entity = new DateRangeEntity([], 'datetime_entity');
        $entity->setMyDateTimeToDateRangeField($startDate, $endDate);
    }
}
