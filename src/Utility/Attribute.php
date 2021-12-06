<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Utility;

final class Attribute
{
    /**
     * Gets the first method attribute of a given type in the stack.
     *
     * @template T
     *
     * @param class-string<T> $attributeClassName
     *   The attribute class
     * @param positive-int $parents
     *   Number of levels up into the stack to traverse
     *
     * @return T|null
     *   An instantiated attribute of the same type passed to $attributeClassName
     *
     * @throws \ReflectionException
     *
     * @internal
     */
    final public static function stack(string $attributeClassName, int $parents = 1)
    {
        $caller = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS)[$parents + 1];
        $reflection = new \ReflectionMethod($caller['class'], $caller['function']);
        $attributes = $reflection->getAttributes($attributeClassName);

        return ($attributes[0] ?? null)?->newInstance();
    }

    /**
     * Gets the first method attribute of a given type on a class.
     *
     * @template T
     *
     * @param class-string $className
     *   The attribute class
     * @param class-string<T> $attributeClassName
     *   The attribute class
     *
     * @return T|null
     *   An instantiated attribute of the same type passed to $attributeClassName
     *
     * @throws \ReflectionException
     *
     * @internal
     */
    final public static function fromClass(string $className, string $attributeClassName)
    {
        $reflection = new \ReflectionClass($className);
        $attributes = $reflection->getAttributes($attributeClassName);

        return ($attributes[0] ?? null)?->newInstance();
    }
}
