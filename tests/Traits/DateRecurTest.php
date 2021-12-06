<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\DateRecurEntity;
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
use Drupal\Core\TypedData\Plugin\DataType\StringData;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList;
use Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem;
use Drupal\datetime\DateTimeComputed;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRecurTrait
 */
final class DateRecurTest extends TestCase
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
            ->willReturn(new \DateTime('14th June 2014 9am', new \DateTimeZone('Asia/Singapore')));
        $ddt2 = $this->createMock(DrupalDateTime::class);
        $ddt2->expects($this->any())
            ->method('getPhpDateTime')
            // Drupal's DDT always use UTC.
            ->willReturn(new \DateTime('14th June 2014 12pm', new \DateTimeZone('Asia/Singapore')));
        $date = $this->createMock(DateTimeComputed::class);
        $date->expects($this->any())
            ->method('getValue')
            ->willReturn($ddt);
        $date2 = $this->createMock(DateTimeComputed::class);
        $date2->expects($this->any())
            ->method('getValue')
            ->willReturn($ddt2);
        $timeZone = $this->createMock(StringData::class);
        $timeZone->expects($this->any())
            ->method('getValue')
            ->willReturn('Asia/Singapore');

        $typedDataManager->expects($this->any())
            ->method('getPropertyInstance')
            ->withConsecutive(
                [$this->isInstanceOf(DateRecurItem::class), 'value', $this->testValue],
                [$this->isInstanceOf(DateRecurItem::class), 'end_value', $this->testEndValue],
                [$this->isInstanceOf(DateRecurItem::class), 'timezone', null],
                [$this->isInstanceOf(DateRecurItem::class), 'start_date', null],
                [$this->isInstanceOf(DateRecurItem::class), 'end_date', null],
            )
            ->willReturnOnConsecutiveCalls(
                $value,
                $value2,
                $timeZone,
                $date,
                $date2,
            );

        $list = new DateRecurFieldItemList($definition);
        $item = new DateRecurItem($definition);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->with($this->isInstanceOf(DateRecurEntity::class), 'my_field', null)
            ->will($this->returnValue($list));
        $fieldTypePluginManager->expects($this->once())
            ->method('createFieldItem')
            ->with($this->isInstanceOf(DateRecurFieldItemList::class), 0, $this->testValue)
            ->willReturn($item);

        $item->set('value', $this->testValue);
        $item->set('end_value', $this->testEndValue);
        // @phpstan-ignore-next-line
        $list->setValue($this->testValue, false);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRecurTrait::getDateTimeFromDateRecurField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Date\DateRecurTrait::doGetDateTimeFromDateRecurField
     */
    public function testGetTimestampAsDateTime(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new DateRecurEntity([], 'datetime_entity');
        $return = $entity->getMyDateRecurFieldAsDateTime();
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
        $startDate = new \DateTimeImmutable('14th June 2014 9am');
        $endDate = new \DateTimeImmutable('14th June 2014 12pm');
        $entity = new DateRecurEntity([], 'datetime_entity');
        $entity->setMyDateTimeToDateRecurField($startDate, $endDate);
    }
}
