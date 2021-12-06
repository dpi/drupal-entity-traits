<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Attribute;

use dpi\DrupalEntityTraits\Attribute\Field;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Attribute\Field
 */
final class FieldTest extends TestCase
{
    public function testAttribute(): void
    {
        $attribute = new Field('foo');
        $this->assertEquals('foo', $attribute->getFieldName());
    }
}
