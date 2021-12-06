<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Exception;

use dpi\DrupalEntityTraits\Exception\ValidationViolationsException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Exception\ValidationViolationsException
 */
final class ValidationViolationsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation('Hello world', '', [], null, 'foo', 'bar'));
        $violations->add(new ConstraintViolation('World hello', '', [], null, 'foo', 'bar'));
        $instance = new ValidationViolationsException($violations);
        $this->assertInstanceOf(\Throwable::class, $instance);
        $this->assertEquals('Violation failures found: Hello world, World hello', $instance->getMessage());
    }
}
