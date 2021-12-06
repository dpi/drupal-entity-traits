<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Core\Fields;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use dpi\DrupalEntityTraits\Utility\Attribute;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Url;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

trait LinkFieldTrait
{
    /**
     * Transforms value of a link field into a Url object.
     *
     * @return \Drupal\Core\Url|null
     *   A URL
     *
     * @throws \InvalidArgumentException
     *   Thrown when the URL object could not be created since source values were malformed
     */
    protected function getUrlFromLinkField(): ?Url
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        return $this->doGetUrlFromLinkField($drupalField->getFieldName());
    }

    /**
     * Transforms value of a link field into a Url object.
     *
     * @param string $fieldName
     *   A field name
     *
     * @return \Drupal\Core\Url|null
     *   A URL
     *
     * @throws \InvalidArgumentException
     *   Thrown when the URL object could not be created since source values were malformed
     *
     * @internal
     */
    private function doGetUrlFromLinkField(string $fieldName): ?Url
    {
        $fieldList = $this->get($fieldName);
        assert($fieldList instanceof FieldItemList);
        $item = $fieldList->first();
        assert(is_null($item) || $item instanceof LinkItem);

        if ($item && !$item->isEmpty()) {
            return $item->getUrl();
        }

        return null;
    }

    /**
     * Sets a URL object to a link field.
     */
    protected function setUrlToLinkField(string $uri): void
    {
        $drupalField = Attribute::stack(Field::class) ?? throw new MissingAttributeException(Field::class);

        $this->doSetUrlToLinkField($uri, $drupalField->getFieldName());
    }

    /**
     * Sets a URL object to a link field.
     *
     * @internal
     */
    private function doSetUrlToLinkField(string $uri, string $fieldName): void
    {
        $this->{$fieldName} = [
            'uri' => $uri,
            'title' => '',
            'options' => [],
        ];
    }
}
