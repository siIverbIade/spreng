<?php

declare(strict_types=1);

namespace Spreng\config\type;

use Spreng\config\type\Config;

/**
 * SystemConfig
 */
class SystemConfig extends Config
{
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getSourcePath(): string
    {
        return $this->getOneConfig('autoloader')['source_path'];
    }

    public function getSourceClass(): string
    {
        return $this->getOneConfig('autoloader')['source_class'];
    }

    public function getFirstRun(): bool
    {
        return $this->getOneConfig('first_run');
    }

    public function setFirstRun(bool $val)
    {
        $this->setOneConfig('first_run', $val);
    }

    public function getIntro(): bool
    {
        return $this->getOneConfig('intro');
    }

    public function setIntro(bool $val)
    {
        $this->setOneConfig('intro', $val);
    }

    public function setSourcePath(string $val)
    {
        $this->config['autoloader']['source_path'] = $val;
    }

    public function setSourceClass(string $val)
    {
        $this->config['autoloader']['source_class'] = $val;
    }

    public function getServicePath(): string
    {
        return $this->getOneConfig('services_path');
    }

    public function setServicePath(string $val)
    {
        $this->setOneConfig('services_path', $val);
    }

    public function getLogFile(): string
    {
        return $this->getOneConfig('log_file');
    }

    public function setLogFile(string $val)
    {
        $this->setOneConfig('log_file', $val);
    }

    public function isLogActive(): bool
    {
        return $this->getLogFile() == '' ? false : true;
    }
}
