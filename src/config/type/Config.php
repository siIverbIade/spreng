<?php

namespace Spreng\config\type;

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
        return isset($this->config[$arg]) ? $this->config[$arg] : null;
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
