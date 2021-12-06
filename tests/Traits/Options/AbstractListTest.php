<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits\Options;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractListTest extends TestCase
{
    protected ContainerBuilder $container;

    /**
     * @var array<mixed, string>
     */
    protected array $allowedValues = [];

    /**
     * @var MockObject|ComplexDataDefinitionInterface
     */
    protected mixed $definition;

    /**
     * @var MockObject|FieldTypePluginManagerInterface
     */
    protected mixed $fieldTypePluginManager;

    public function setup(): void
    {
        parent::setup();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $this->fieldTypePluginManager = $this->createMock(FieldTypePluginManagerInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);
        $this->container->set('plugin.manager.field.field_type', $this->fieldTypePluginManager);

        $fieldStorageDefinition = $this->createMock(FieldStorageDefinitionInterface::class);
        $fieldStorageDefinition->expects($this->any())
            ->method('getSetting')
            ->with('allowed_values')
            ->will($this->returnCallback(function () {
                return $this->allowedValues;
            }));
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
            ->with('list_entity')
            ->willReturn($entityType);

        $this->definition = $this->createMock(ComplexDataDefinitionInterface::class);
        $this->definition->expects($this->any())
            ->method('getPropertyDefinitions')
            ->willReturn([
                'value' => $this->createMock(DataDefinitionInterface::class),
            ]);
    }
}
