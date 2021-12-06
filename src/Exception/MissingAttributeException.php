<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Exception;

use Throwable;

class MissingAttributeException extends \Exception
{
    /**
     * Creates an exception for when an attribute is expected.
     *
     * @param class-string $className
     *   The missing attribute class
     * @param \Throwable|null $previous
     *   The previous throwable used for the exception chaining
     */
    public function __construct(public string $className, Throwable $previous = null)
    {
        parent::__construct(sprintf('Missing attribute of type %s', $this->className), 0, $previous);
    }
}
