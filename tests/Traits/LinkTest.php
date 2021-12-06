<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Tests\fixtures\Entity\LinkEntity;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\Plugin\DataType\Uri;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldType\LinkItem;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait
 */
final class LinkTest extends TestCase
{
    private ContainerBuilder $container;
    private LinkItem $item;

    /**
     * @var \Drupal\Core\Field\FieldItemList|\PHPUnit\Framework\MockObject\MockObject
     */
    private mixed $list;
    private string $testValue = 'http://example.com/foo/bar.html';

    public function setUp(): void
    {
        parent::setUp();

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

        $uri = $this->createMock(Uri::class);
        $uri->expects($this->any())
            ->method('getValue')
            ->willReturn($this->testValue);

        $typedDataManager->expects($this->any())
            ->method('getPropertyInstance')
            ->with($this->isInstanceOf(LinkItem::class), 'uri', null)
            ->willReturn($uri);

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

        $definition = $this->createMock(ComplexDataDefinitionInterface::class);
        $definition->expects($this->any())
            ->method('getPropertyDefinitions')
            ->willReturn([
                'value' => $this->createMock(DataDefinitionInterface::class),
            ]);
        $this->list = $this->getMockBuilder(FieldItemList::class)
            ->setConstructorArgs([$definition])
            ->onlyMethods(['setValue', 'get'])
            ->getMock();
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->will($this->returnValue($this->list));

        $this->item = new LinkItem($definition);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItem')
            ->with($this->isInstanceOf(FieldItemList::class), 0, $this->testValue)
            ->willReturn($this->item);

        $this->item->set('value', $this->testValue);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait::getUrlFromLinkField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait::doGetUrlFromLinkField
     */
    public function testGetMyUrlFromLinkField(): void
    {
        $this->list->expects($this->any())
            ->method('get')
            ->with(0)
            ->willReturn($this->item);

        \Drupal::setContainer($this->container);
        $entity = new LinkEntity([], 'test_entity');
        $url = $entity->getMyUrlFromLinkField();
        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('http://example.com/foo/bar.html', $url->getUri());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait::getUrlFromLinkField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait::doGetUrlFromLinkField
     */
    public function testGetMyUrlFromLinkFieldUndefined(): void
    {
        $this->list->expects($this->any())
            ->method('get')
            ->with(0)
            ->willReturn(null);

        \Drupal::setContainer($this->container);
        $entity = new LinkEntity([], 'test_entity');
        $this->assertNull($entity->getMyUrlFromLinkField());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait::setUrlToLinkField
     * @covers \dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait::doSetUrlToLinkField
     */
    public function testSetUrlToLinkField(): void
    {
        $this->list->expects($this->once())
            ->method('setValue')
            ->with([
                'uri' => 'http://example.com/foo/bar.html',
                'title' => '',
                'options' => [],
        ], true);

        \Drupal::setContainer($this->container);
        $entity = new LinkEntity([], 'test_entity');
        $entity->setMyUrlToLinkField('http://example.com/foo/bar.html');
    }
}
