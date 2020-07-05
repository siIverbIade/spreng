<?php

declare(strict_types=1);

namespace Spreng\system\loader;

use Spreng\system\loader\iSpreng;

/**
 * iNject
 */
abstract class iNject implements iSpreng
{
    public static function getFn(array $excludeFn = []): array
    {
        return array_diff(get_class_methods(get_called_class()), array_merge($excludeFn, ['getFn', '__construct']));
    }
}
