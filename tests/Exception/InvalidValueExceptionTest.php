<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Exception;

use dpi\DrupalEntityTraits\Exception\InvalidValueException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Exception\InvalidValueException
 */
final class InvalidValueExceptionTest extends TestCase
{
    public function testException(): void
    {
        $instance = new InvalidValueException();
        $this->assertInstanceOf(\Throwable::class, $instance);
    }
}
