<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Exception;

use dpi\DrupalEntityTraits\Exception\EntityValidationViolations;
use Drupal\Core\Entity\EntityConstraintViolationList;
use Drupal\Core\Entity\FieldableEntityInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Exception\EntityValidationViolations
 */
final class EntityValidationViolationsTest extends TestCase
{
    public function testException(): void
    {
        $entity = $this->createMock(FieldableEntityInterface::class);
        $entity->expects($this->exactly(2))
            ->method('hasField')
            ->withConsecutive(
                ['foo'],
                ['bar']
            )
            ->willReturn(true);

        $violations = new EntityConstraintViolationList($entity);
        $violations->add(new ConstraintViolation('Hello world', '', [], null, 'foo', 'bar'));
        $violations->add(new ConstraintViolation('Hello world', '', [], null, 'bar', 'bar'));

        $instance = new EntityValidationViolations($violations);
        $this->assertInstanceOf(\Throwable::class, $instance);
        $this->assertEquals('Entity has violation failures in foo, bar', $instance->getMessage());
    }
}
