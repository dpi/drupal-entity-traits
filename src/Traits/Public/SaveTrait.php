<?php

declare(strict_types=1);

namespace dpi\DrupalEntityTraits\Traits\Public;

use dpi\DrupalEntityTraits\Traits\Generic\SaveTrait as GenericSaveTrait;

trait SaveTrait
{
    use GenericSaveTrait {
        saveAndValidate as public save;
    }
}
