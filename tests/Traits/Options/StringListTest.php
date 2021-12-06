<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits\Options;

use dpi\DrupalEntityTraits\Exception\ValidationException;
use dpi\DrupalEntityTraits\Tests\fixtures\Entity\Options\StringListEntity;
use Drupal\Core\Field\FieldItemList;
use Drupal\options\Plugin\Field\FieldType\ListStringItem;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait
 */
final class StringListTest extends AbstractListTest
{
    protected array $allowedValues = [
        'key_a' => 'Label A',
        'key_b' => 'Label B',
        'key_c' => 'Label C',
    ];

    /**
     * @var FieldItemList<ListStringItem>|\PHPUnit\Framework\MockObject\MockObject
     */
    private mixed $list;

    public function setup(): void
    {
        parent::setup();

        $item = new ListStringItem($this->definition);
        $item->set('value', 'key_a');
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
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait::getKeyFromStringListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetKeyFromListField
     */
    public function testGetKeyFromListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new StringListEntity([], 'list_entity');
        $this->assertEquals('key_a', $entity->getMyKeyFromStringListField());

        // Test when the value in storage is no longer an option.
        unset($this->allowedValues['key_a']);
        $this->expectException(ValidationException::class);
        $this->assertEquals('key_a', $entity->getMyKeyFromStringListField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait::getKeyFromStringListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetKeyFromListField
     */
    public function testGetKeyFromListFieldSafe(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new StringListEntity([], 'list_entity');
        $this->assertEquals('key_a', $entity->getMyKeyFromListFieldSafe());

        // Test when the value in storage is no longer an option.
        unset($this->allowedValues['key_a']);
        $this->assertNull($entity->getMyKeyFromListFieldSafe());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait::getLabelFromStringListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doGetLabelFromListField
     */
    public function testGetLabelFromListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new StringListEntity([], 'list_entity');
        $this->assertEquals('Label A', $entity->getMyLabelFromStringListField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait::setValueToStringListField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\BaseListFieldTrait::doSetValueToListField
     */
    public function testSetValueToListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new StringListEntity([], 'list_entity');

        $this->list->expects($this->once())
            ->method('setValue')
            ->with('key_a');
        $entity->setMyValueToStringListField('key_a');
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\Options\StringListFieldTrait::getAllowedValuesOfStringListField
     */
    public function testGetAllowedValuesOfListField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new StringListEntity([], 'list_entity');
        $this->assertEquals([
            'key_a' => 'Label A',
            'key_b' => 'Label B',
            'key_c' => 'Label C',
        ], $entity->getMyAllowedValuesOfStringListField());
    }
}
