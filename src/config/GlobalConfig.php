<?php

declare(strict_types=1);

namespace Spreng\config;

use Spreng\config\type\ComposerConfig;
use Spreng\config\ParseConfig;
use Spreng\config\type\HttpConfig;
use Spreng\config\type\ModelConfig;
use Spreng\config\type\SystemConfig;
use Spreng\config\type\SecurityConfig;
use Spreng\system\loader\SprengClasses;
use Spreng\config\type\ConnectionConfig;

/**
 * GlobalConfig
 */
class GlobalConfig extends ParseConfig
{
    public static function getConfig(string $type): array
    {
        $config = [];
        if (isset($_ENV["APPLICATION"])) {
            $config = $_ENV["APPLICATION"];
        } else {
            $config = self::loadConfig($type)->getConfig();
            $_ENV["APPLICATION"] = $config;
        }
        return $config;
    }

    public static function getComposerConfig(): ComposerConfig
    {
        $config = [];
        if (isset($_ENV['COMPOSER'])) {
            $config = $_ENV['COMPOSER'];
        } else {
            $composer = self::autoLoad();
            $config = $composer->getConfig();
            $_ENV['config_composer'] = $config;
        }
        return $composer;
    }

    public static function getConnectionConfig(): ConnectionConfig
    {
        return new ConnectionConfig(self::getConfig('connection'));
    }

    public static function getHttpConfig(): HttpConfig
    {
        return new HttpConfig(self::getConfig('http'));
    }

    public static function getModelConfig(): ModelConfig
    {
        return new ModelConfig(self::getConfig('model'));
    }

    public static function getSecurityConfig(): SecurityConfig
    {
        return new SecurityConfig(self::getConfig('security'));
    }

    public static function getSystemConfig(): SystemConfig
    {
        return new SystemConfig(self::getConfig('system'));
    }

    public static function getAllImplementationsOf(string $baseFolder, string $class): array
    {
        if (!isset($_ENV["config_classes_$class"])) {
            $_ENV["config_classes_$class"] = SprengClasses::scanFromSource($baseFolder, $class);
        }
        return $_ENV["config_classes_$class"];
    }

    public static function setConnectionConfig(ConnectionConfig $config)
    {
        parent::setConnectionConfig($config);
        $_ENV['config_connection'] = $config->getConfig();
    }

    public static function setHttpConfig(HttpConfig $config)
    {
        parent::setHttpConfig($config);
        $_ENV['config_http'] = $config->getConfig();
    }

    public static function setModelConfig(ModelConfig $config)
    {
        parent::setModelConfig($config);
        $_ENV['config_model'] = $config->getConfig();
    }

    public static function setSecurityConfig(SecurityConfig $config)
    {
        parent::setSecurityConfig($config);
        $_ENV['config_security'] = $config->getConfig();
    }

    public static function setSystemConfig(SystemConfig $config)
    {
        parent::setSystemConfig($config);
        $_ENV['config_system'] = $config->getConfig();
    }

    public static function saveConfig(string $type, array $config)
    {
        parent::saveConfig($type, $config);
        $_ENV["config_$type"] = $config;
    }

    public static function mergeConfig(string $type, array $config)
    {
        $currentConfig = self::getConfig($type);
        $newConfig = array_replace_recursive($currentConfig, $config);
        parent::saveConfig($type, $newConfig);
        $_ENV["config_$type"] = $newConfig;
    }

    public static function clearAll()
    {
        if (isset($_ENV)) unset($_ENV);
    }
}
