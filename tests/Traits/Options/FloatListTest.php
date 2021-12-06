<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits\Options;

use dpi\DrupalEntityTraits\Exception\ValidationException;
use dpi\DrupalEntityTraits\Tests\fixtures\Entity\Options\FloatListEntity;
use Drupal\Core\Field\FieldItemList;
use Drupal\options\Plugin\Field\FieldType\ListFloatItem;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait
 */
final class FloatListTest extends AbstractListTest
{
    protected array $allowedValues = [
        '1.3' => 'Label A',
        '2.0' => 'Label B',
        '3.3' => 'Label C',
    ];

    /**
     * @var FieldItemList<ListFloatItem>|\PHPUnit\Framework\MockObject\MockObject
     */
    private mixed $list;

    public function setup(): void
    {
        parent::setup();

        $item = new ListFloatItem($this->definition);
        $item->set('value', '1.3');
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
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait::getKeyFromFloatListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetKeyFromListField
     */
    public function testGetKeyFromListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new FloatListEntity([], 'list_entity');
        $this->assertEquals(1.3, $entity->getMyKeyFromFloatListField());

        // Test when the value in storage is no longer an option.
        unset($this->allowedValues['1.3']);
        $this->expectException(ValidationException::class);
        $this->assertEquals(1.3, $entity->getMyKeyFromFloatListField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait::getKeyFromFloatListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetKeyFromListField
     */
    public function testGetKeyFromListFieldSafe(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new FloatListEntity([], 'list_entity');
        $this->assertEquals('1.3', $entity->getMyKeyFromFloatListFieldSafe());

        // Test when the value in storage is no longer an option.
        unset($this->allowedValues['1.3']);
        $this->assertNull($entity->getMyKeyFromFloatListFieldSafe());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait::getLabelFromFloatListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetLabelFromListField
     */
    public function testGetLabelFromListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new FloatListEntity([], 'list_entity');
        $this->assertEquals('Label A', $entity->getMyLabelFromFloatListField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait::setValueToFloatListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doSetValueToListField
     */
    public function testSetValueToListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new FloatListEntity([], 'list_entity');

        $this->list->expects($this->once())
            ->method('setValue')
            ->with(4.4);
        $entity->setMyValueToFloatListField(4.4);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\FloatListFieldTrait::getAllowedValuesOfFloatListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetAllowedValuesOfListField
     */
    public function testGetAllowedValuesOfListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new FloatListEntity([], 'list_entity');
        $this->assertEquals([
            '1.3' => 'Label A',
            '2.0' => 'Label B',
            '3.3' => 'Label C',
        ], $entity->getMyAllowedValuesOfFloatListField());
    }
}
