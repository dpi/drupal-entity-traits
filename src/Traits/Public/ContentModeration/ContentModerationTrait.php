<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Public\ContentModeration;

use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Traits\Core\ContentModeration\ContentModerationTrait as CoreContentModerationTrait;

/**
 * Public content moderation trait.
 *
 * Use this trait in combination with the interface.
 *
 * @see \dpi\DrupalEntityTraits\Interface\ContentModerationInterface
 */
trait ContentModerationTrait
{
    use CoreContentModerationTrait;

    /**
     * Get the moderation state.
     *
     * @return string|null
     *   The moderation state machine name
     */
    public function getModerationState(): ?string
    {
        return $this->getStateFromContentModerationField();
    }

    /**
     * Get the moderation state label.
     *
     * @return string|null
     *   The moderation state label, or null if the state does not exist
     */
    public function getModerationStateLabel(): ?string
    {
        return $this->getLabelFromContentModerationField();
    }

    /**
     * Set the moderation state.
     *
     * @param string $state
     *   The moderation state machine name
     *
     * @return $this
     *   Returns this entity for chaining
     *
     * @throws \dpi\DrupalEntityTraits\Exception\InvalidValueException
     *   Thrown when the state is invalid for this entity
     */
    #[Validate]
    public function setModerationState(string $state)
    {
        $this->setStateToContentModerationField($state);

        return $this;
    }
}
