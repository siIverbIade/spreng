<?php

namespace Spreng\system\loader;

interface iSpreng
{
    public static function getFn(array $excludeFn = []): array;
}
