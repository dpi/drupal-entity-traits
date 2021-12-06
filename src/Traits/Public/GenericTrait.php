<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Public;

use dpi\DrupalEntityTraits\Traits\Generic\GenericTrait as GenericGenericTrait;

/**
 * Functionality that applies to all content entities.
 */
trait GenericTrait
{
    use GenericGenericTrait {
        getEntityStorage as public entityStorage;
    }

    /**
     * Saves this entity and returns for chaining.
     *
     * @param int|null $result
     *   If passed, will be updated with the result of the save operation
     *
     * @return $this
     *   Returns this for chaining
     */
    public function saveAnd(?int &$result = 0)
    {
        $this->saveResultByReference($result);

        return $this;
    }
}
