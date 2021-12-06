<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Interface\ContentModerationInterface;
use dpi\DrupalEntityTraits\Traits\Public\ContentModeration\ContentModerationTrait;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Test entity for content moderation trait and interface.
 */
final class ContentModerationEntity extends ContentEntityBase implements ContentModerationInterface
{
    use ContentModerationTrait {
        getWorkflowFromContentModerationField as public getContentModerationWorkflow;
        getStatesFromContentModerationField as public getContentModerationStates;
    }

    public function id()
    {
        return 123;
    }

    public function bundle()
    {
        return 'testbundle';
    }
}
