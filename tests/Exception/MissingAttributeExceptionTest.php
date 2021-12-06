<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Exception;

use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Exception\MissingAttributeException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Exception\MissingAttributeException
 */
final class MissingAttributeExceptionTest extends TestCase
{
    public function testException(): void
    {
        $instance = new MissingAttributeException(Field::class);
        $this->assertInstanceOf(\Throwable::class, $instance);
        $this->assertEquals('Missing attribute of type dpi\DrupalEntityTraits\Attribute\Field', $instance->getMessage());
    }
}
