<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Interface;

/**
 * @see \dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait
 */
interface ContentModerationInterface
{
    /**
     * Get the moderation state.
     *
     * @return string
     *   The moderation state machine name
     */
    public function getModerationState(): ?string;

    /**
     * Get the moderation state label.
     *
     * @return string|null
     *   The moderation state label, or null if the state does not exist
     */
    public function getModerationStateLabel(): ?string;

    /**
     * Set the moderation state.
     *
     * @param string $state
     *   The moderation state machine name
     *
     * @return $this
     *   Returns this entity for chaining
     */
    public function setModerationState(string $state);
}
