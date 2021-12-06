<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Traits;

use dpi\DrupalEntityTraits\Exception\InvalidValueException;
use dpi\DrupalEntityTraits\Tests\fixtures\Entity\ContentModerationEntity;
use Drupal\content_moderation\Entity\ContentModerationStateInterface;
use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\content_moderation\Plugin\Field\ModerationStateFieldItemList;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\DataType\EntityAdapter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\workflows\StateInterface;
use Drupal\workflows\WorkflowInterface;
use Drupal\workflows\WorkflowTypeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait
 * @covers \dpi\DrupalEntityTraits\Traits\Core\ContentModeration\ContentModerationTrait
 * @covers \dpi\DrupalEntityTraits\Interface\ContentModerationInterface
 */
final class ContentModerationTest extends TestCase
{
    private ContainerBuilder $container;

    /**
     * @var ModerationStateFieldItemList<StringItem>
     */
    private ModerationStateFieldItemList $list;

    public function setup(): void
    {
        parent::setup();

        $entityFieldManager = $this->createMock(EntityFieldManagerInterface::class);
        $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
        $fieldTypePluginManager = $this->createMock(FieldTypePluginManagerInterface::class);
        $moderationInformation = $this->createMock(ModerationInformationInterface::class);

        $this->container = new ContainerBuilder();
        $this->container->set('entity_field.manager', $entityFieldManager);
        $this->container->set('entity_type.manager', $entityTypeManager);
        $this->container->set('plugin.manager.field.field_type', $fieldTypePluginManager);
        $this->container->set('content_moderation.moderation_information', $moderationInformation);

        $fieldStorageDefinition = $this->createMock(FieldStorageDefinitionInterface::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinitionInterface::class)->getMock();
        $fieldDefinition->expects($this->any())
            ->method('getFieldStorageDefinition')
            ->willReturn($fieldStorageDefinition);
        $entityFieldManager->expects($this->any())
            ->method('getFieldDefinitions')
            ->willReturn(['moderation_state' => $fieldDefinition]);

        $entityType = $this->createMock('\Drupal\Core\Entity\EntityTypeInterface');
        $entityType->expects($this->any())
            ->method('getKeys')
            ->will($this->returnValue([
                'id' => 'id',
            ]));
        $entityTypeManager
            ->expects($this->any())
            ->method('getDefinition')
            ->with('content_moderation_entity')
            ->willReturn($entityType);

        $definition = $this->createMock(ComplexDataDefinitionInterface::class);
        $definition->expects($this->any())
            ->method('getPropertyDefinitions')
            ->willReturn([
                'value' => $this->createMock(DataDefinitionInterface::class),
            ]);

        $this->list = $this->getMockBuilder(ModerationStateFieldItemList::class)
            ->setConstructorArgs([$definition])
            ->onlyMethods(['loadContentModerationStateRevision'])
            ->getMock();

        $contentModerationState = $this->createMock(ContentModerationStateInterface::class);
        // @phpstan-ignore-next-line
        $contentModerationState->moderation_state = (object) ['value' => 'draft'];
        $this->list->expects($this->any())
            ->method('loadContentModerationStateRevision')
            ->willReturn($contentModerationState);

        $moderationInformation->expects($this->any())
            ->method('shouldModerateEntitiesOfBundle')
            ->willReturn(true);
        $workflow = $this->createMock(WorkflowInterface::class);
        $moderationInformation->expects($this->any())
            ->method('getWorkflowForEntity')
            ->willReturn($workflow);

        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItemList')
            ->willReturn($this->list);

        $item = new StringItem($definition);
        $fieldTypePluginManager->expects($this->any())
            ->method('createFieldItem')
            ->willReturn($item);
        $item->set('value', 'needs_review');

        $workflowTypePlugin = $this->createMock(WorkflowTypeInterface::class);
        $workflow->expects($this->any())
            ->method('getTypePlugin')
            ->willReturn($workflowTypePlugin);

        $state = $this->createMock(StateInterface::class);
        $workflowTypePlugin->expects($this->any())
            ->method('getStates')
            ->willReturn([
                'needs_review' => $state,
                'published' => $state,
            ]);

        $state->expects($this->any())
            ->method('label')
            ->willReturn('Needs Review');
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait::getModerationState
     */
    public function testGetModerationState(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new ContentModerationEntity([], 'content_moderation_entity');
        $parent = EntityAdapter::createFromEntity($entity);
        $this->list->setContext(null, $parent);

        $this->assertEquals('needs_review', $entity->getModerationState());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait::getModerationStateLabel
     */
    public function testGetModerationStateLabel(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new ContentModerationEntity([], 'content_moderation_entity');
        $parent = EntityAdapter::createFromEntity($entity);
        $this->list->setContext(null, $parent);

        $this->assertEquals('Needs Review', $entity->getModerationStateLabel());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait::setModerationState
     */
    public function testSetModerationState(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new ContentModerationEntity([], 'content_moderation_entity');
        $parent = EntityAdapter::createFromEntity($entity);
        $this->list->setContext(null, $parent);

        $entity->setModerationState('published');
        // @phpstan-ignore-next-line
        $this->assertEquals('published', $entity->moderation_state->value);
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait::setModerationState
     */
    public function testSetModerationStateNotInWorkflow(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new ContentModerationEntity([], 'content_moderation_entity');
        $parent = EntityAdapter::createFromEntity($entity);
        $this->list->setContext(null, $parent);

        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('State "blah" is not a valid state in this workflow');
        $entity->setModerationState('blah');
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait::getStateFromContentModerationField
     */
    public function testGetStateFromContentModerationField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new ContentModerationEntity([], 'content_moderation_entity');
        $this->assertArrayHasKey('needs_review', $entity->getContentModerationStates());
    }

    /**
     * @covers \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait::getWorkflowFromContentModerationField
     */
    public function testGetWorkflowFromContentModerationField(): void
    {
        \Drupal::setContainer($this->container);
        $entity = new ContentModerationEntity([], 'content_moderation_entity');
        $this->assertInstanceOf(WorkflowInterface::class, $entity->getContentModerationWorkflow());
    }
}
