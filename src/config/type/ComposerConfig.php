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
        return key($this->config['autoload']['psr-4']);
    }

    public function getPsr4Source(): string
    {
        return str_replace('/', '', array_values($this->config['autoload']['psr-4'])[0]);
    }

    public function isAutoloadSet(): bool
    {
        return (isset($this->config['autoload']['psr-4']) && $this->config['autoload']['psr-4'] !== []);
    }
}
