<?php

declare(strict_types=1);

namespace Spreng\http;

use Spreng\system\loader\iNject;

/**
 * Controller
 */
abstract class Controller extends iNject
{
    protected $rootUrl = '';
    protected $require = ['DEFAULT'];

    public function getRootUrl(): string
    {
        return $this->rootUrl;
    }

    public function getRequiredPermissions(): array
    {
        return $this->require;
    }

    public static function getFn(array $excludeFn = []): array
    {
        return parent::getFn(['getRootUrl', 'getRequiredPermissions']);
    }
}
