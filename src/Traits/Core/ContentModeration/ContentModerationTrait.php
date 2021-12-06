<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\ContentModeration;

use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Exception\InvalidValueException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\content_moderation\ModerationInformationInterface;
use Drupal\content_moderation\Plugin\Field\ModerationStateFieldItemList;
use Drupal\workflows\WorkflowInterface;

/**
 * @see \Drupal\Core\Field\Plugin\Field\FieldType\StringItem
 * @see \Drupal\content_moderation\Plugin\Field\ModerationStateFieldItemList
 */
trait ContentModerationTrait
{
    /**
     * Get the moderation state.
     *
     * [#Validate] can be used to ensure state is valid.
     *
     * @return string|null
     *   The moderation state machine name
     */
    protected function getStateFromContentModerationField(): ?string
    {
        $fieldList = $this->get('moderation_state');
        assert($fieldList instanceof ModerationStateFieldItemList);
        /** @var string|null $moderationState */
        // @phpstan-ignore-next-line
        $moderationState = $fieldList->value ?? null;
        $validate = Attribute::stack(Validate::class);

        if (true === $validate?->throw && !in_array($moderationState, array_keys($this->getStatesFromContentModerationField()), true)) {
            throw new InvalidValueException(sprintf('State "%s" is not a valid state in this workflow', $moderationState ?? ''));
        }

        return $moderationState;
    }

    /**
     * Get the moderation state label.
     *
     * [#Validate] can be used to ensure state is valid.
     *
     * @return string|null
     *   The moderation state label, or null if the state does not exist
     */
    protected function getLabelFromContentModerationField(): ?string
    {
        $fieldList = $this->get('moderation_state');
        assert($fieldList instanceof ModerationStateFieldItemList);
        /** @var string|null $moderationState */
        // @phpstan-ignore-next-line
        $moderationState = $fieldList->value ?? null;
        $validate = Attribute::stack(Validate::class);

        $label = ($this->getStatesFromContentModerationField()[$moderationState] ?? null)?->label();
        if (!$label && true === $validate?->throw) {
            throw new InvalidValueException(sprintf('State "%s" is not a valid state in this workflow', $moderationState ?? ''));
        }

        return $label;
    }

    /**
     * Set the moderation state.
     *
     * [#Validate] can be used to ensure state value is valid.
     *
     * @param string $state
     *   The moderation state machine name
     *
     * @throws \dpi\DrupalEntityTraits\Exception\InvalidValueException
     *   Thrown when validation exceptions are on and the state is invalid for this entity
     */
    protected function setStateToContentModerationField(string $state): void
    {
        $validate = Attribute::stack(Validate::class);
        if ($validate && !in_array($state, array_keys($this->getStatesFromContentModerationField()), true)) {
            !$validate->throw ?: throw new InvalidValueException(sprintf('State "%s" is not a valid state in this workflow', $state));
        }

        // @phpstan-ignore-next-line
        $this->moderation_state->value = $state;
    }

    /**
     * Get the allowed workflow states for this entity.
     *
     * The keys are often more useful than the values of this method.
     *
     * @return \Drupal\workflows\StateInterface[]
     *   The allowed workflow states for this entity keyed by state ID
     */
    protected function getStatesFromContentModerationField(): array
    {
        return $this->getWorkflowFromContentModerationField()?->getTypePlugin()?->getStates() ?? [];
    }

    /**
     * Get the workflow that applies to this entity.
     *
     * @return \Drupal\workflows\WorkflowInterface|null
     *   An entity, or NULL if no workflow applies to this entity
     */
    protected function getWorkflowFromContentModerationField(): ?WorkflowInterface
    {
        $moderationInformation = \Drupal::service('content_moderation.moderation_information');
        assert($moderationInformation instanceof ModerationInformationInterface);

        return $moderationInformation->getWorkflowForEntity($this);
    }
}
