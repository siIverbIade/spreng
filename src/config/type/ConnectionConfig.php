<?php

declare(strict_types=1);

namespace Spreng\config\type;

use Spreng\config\type\Config;

/**
 * ConnectionConfig
 */
class ConnectionConfig extends Config
{

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getUrl(string $origin): string
    {
        return $this->getOneConfig($origin)['url'];
    }

    public function getPort(string $origin): string
    {
        return $this->getOneConfig($origin)['port'];
    }

    public function getDatabase(string $origin): string
    {
        return $this->getOneConfig($origin)['database'];
    }

    public function getUser(string $origin): string
    {
        return $this->getOneConfig($origin)['user'];
    }

    public function getPassword(string $origin): string
    {
        return $this->getOneConfig($origin)['password'];
    }

    public function getRegenerate(string $origin): bool
    {
        return $this->getOneConfig($origin)['regenerate'];
    }

    public function getPoolConfig(string $origin): array
    {
        return $this->getConfig()[$origin];
    }

    public function setUrl(string $origin, string $val)
    {
        $this->config[$origin]['url'] = $val;
    }

    public function setPort(string $origin, string $val)
    {
        $this->config[$origin]['port'] = $val;
    }

    public function setDatabase(string $origin, string $val)
    {
        $this->config[$origin]['database'] = $val;
    }

    public function setUser(string $origin, string $val)
    {
        $this->config[$origin]['user'] = $val;
    }

    public function setPassword(string $origin, string $val)
    {
        $this->config[$origin]['password'] = $val;
    }
}
