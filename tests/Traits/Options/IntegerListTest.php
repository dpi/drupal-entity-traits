<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits\Options;

use dpi\DrupalEntityTraits\Exception\ValidationException;
use dpi\DrupalEntityTraits\Tests\fixtures\Entity\Options\IntegerListEntity;
use Drupal\Core\Field\FieldItemList;
use Drupal\options\Plugin\Field\FieldType\ListIntegerItem;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait
 */
final class IntegerListTest extends AbstractListTest
{
    protected array $allowedValues = [
        '-3' => 'Label A',
        '4' => 'Label B',
        '77' => 'Label C',
    ];

    /**
     * @var FieldItemList<ListIntegerItem>|\PHPUnit\Framework\MockObject\MockObject
     */
    private mixed $list;

    public function setup(): void
    {
        parent::setup();

        $item = new ListIntegerItem($this->definition);
        $item->set('value', '4');
        $this->list = $this->createMock(FieldItemList::class);
        $this->list->expects($this->any())
            ->method('get')
            ->with(0)
            ->willReturn($item);
        $this->fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->will($this->returnValue($this->list));
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait::getKeyFromIntegerListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetKeyFromListField
     */
    public function testGetKeyFromListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new IntegerListEntity([], 'list_entity');
        $this->assertEquals(4, $entity->getMyKeyFromIntegerListField());

        // Test when the value in storage is no longer an option.
        unset($this->allowedValues['4']);
        $this->expectException(ValidationException::class);
        $this->assertEquals(4, $entity->getMyKeyFromIntegerListField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait::getKeyFromIntegerListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetKeyFromListField
     */
    public function testGetKeyFromListFieldSafe(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new IntegerListEntity([], 'list_entity');
        $this->assertEquals(4, $entity->getMyKeyFromIntegerListFieldSafe());

        // Test when the value in storage is no longer an option.
        unset($this->allowedValues['4']);
        $this->assertNull($entity->getMyKeyFromIntegerListFieldSafe());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait::getLabelFromIntegerListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetLabelFromListField
     */
    public function testGetLabelFromListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new IntegerListEntity([], 'list_entity');
        $this->assertEquals('Label B', $entity->getMyLabelFromIntegerListField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait::setValueToIntegerListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doSetValueToListField
     */
    public function testSetValueToListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new IntegerListEntity([], 'list_entity');

        $this->list->expects($this->once())
            ->method('setValue')
            ->with('123');
        $entity->setMyValueToIntegerListField(123);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\IntegerListFieldTrait::getAllowedValuesOfIntegerListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetAllowedValuesOfListField
     */
    public function testGetAllowedValuesOfListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new IntegerListEntity([], 'list_entity');
        $this->assertEquals([
            -3 => 'Label A',
            4 => 'Label B',
            77 => 'Label C',
        ], $entity->getMyAllowedValuesOfIntegerListField());
    }
}
