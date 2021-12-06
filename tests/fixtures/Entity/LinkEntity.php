<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\fixtures\Entity;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Traits\Core\Fields\LinkFieldTrait;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Url;

/**
 * Test entity for link trait.
 */
final class LinkEntity extends ContentEntityBase
{
    use LinkFieldTrait;

    public function bundle()
    {
        return 'testbundle';
    }

    #[Field('my_field')]
    public function getMyUrlFromLinkField(): ?Url
    {
        return $this->getUrlFromLinkField();
    }

    /**
     * @return $this
     */
    #[Field('my_field')]
    public function setMyUrlToLinkField(string $uri)
    {
        $this->setUrlToLinkField($uri);

        return $this;
    }
}
