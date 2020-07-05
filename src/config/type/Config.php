<?php

namespace Spreng\config\type;

use Spreng\system\log\Logger;

/**
 * Config
 */
abstract class Config
{
    public $config;

    public function assets(array $def, array $config)
    {
        $this->config = array_replace_recursive($def, $config);
    }

    public function getOneConfig(string $arg)
    {
        return $this->config[$arg];
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setOneConfig(string $arg, $val)
    {
        return $this->config[$arg] = $val;
    }

    public function setConfig($val)
    {
        $this->config = $val;
    }

    public function mergeConfig(array $val)
    {
        $this->config = array_merge(isset($this->config) ? $this->config : [], $val);
    }
}
