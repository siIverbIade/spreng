<?php

declare(strict_types=1);

namespace Spreng\config\type;

use Spreng\config\type\Config;

/**
 * HttpConfig
 */
class HttpConfig extends Config
{
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getListenPort(): string
    {
        return $this->getOneConfig('listen_port');
    }

    public function setListenPort(string $val)
    {
        return $this->setOneConfig('listen_port', $val);
    }

    public function getControllersPath(): string
    {
        return $this->getOneConfig('controllers_path');
    }

    public function setControllersPath(string $val)
    {
        return $this->setOneConfig('controllers_path', $val);
    }
}
