<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Tests\Utility;

use dpi\DrupalEntityTraits\Attribute\EntityValidateOnSave;
use dpi\DrupalEntityTraits\Attribute\Field;
use dpi\DrupalEntityTraits\Attribute\Validate;
use dpi\DrupalEntityTraits\Utility\Attribute;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \dpi\DrupalEntityTraits\Utility\Attribute
 */
final class AttributeTest extends TestCase
{
    /**
     * @covers ::stack
     */
    public function testStack(): void
    {
        $obj = new class() {
            #[Field('my_field_name')]
            public function method1(): ?Field
            {
                return $this->method2();
            }

            protected function method2(): ?Field
            {
                return Attribute::stack(Field::class);
            }
        };

        $this->assertEquals('my_field_name', $obj->method1()?->getFieldName());
    }

    /**
     * @covers ::stack
     */
    public function testStackAttributeNotExists(): void
    {
        $obj = new class() {
            #[Field('my_field_name')]
            public function method1(): ?Validate
            {
                return $this->method2();
            }

            protected function method2(): ?Validate
            {
                return Attribute::stack(Validate::class);
            }
        };

        $this->assertNull($obj->method1());
    }

    /**
     * @covers ::stack
     */
    public function testStackParents(): void
    {
        $obj = new class() {
            #[Field('my_field_name')]
            public function method1(): ?Field
            {
                return $this->method2();
            }

            protected function method2(): ?Field
            {
                return $this->method3();
            }

            protected function method3(): ?Field
            {
                return Attribute::stack(Field::class, 2);
            }
        };

        $this->assertEquals('my_field_name', $obj->method1()?->getFieldName());
    }

    /**
     * @covers ::fromClass
     */
    public function testFromClass(): void
    {
        $obj = new #[EntityValidateOnSave] class() {
        };
        $attribute = Attribute::fromClass($obj::class, EntityValidateOnSave::class);
        $this->assertInstanceOf(EntityValidateOnSave::class, $attribute);
    }
}
