<?php

declare(strict_types=1);

namespace Spreng\config\type;

use Spreng\config\type\Config;

class ComposerConfig extends Config
{
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getPsr4Name(): string
    {
        return key($this->getOneConfig('autoload')['psr-4']);
    }

    public function getPsr4Source(): string
    {
        return str_replace('/', '', array_values($this->getOneConfig('autoload')['psr-4'])[0]);
    }
}
