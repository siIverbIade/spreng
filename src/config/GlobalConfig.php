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

    public static function getJsonEnv(): array
    {
        return json_decode($_ENV["APPLICATION"], true);
    }

    public static function getConfig(string $type): array
    {
        $config = [];
        if (isset($_ENV["APPLICATION"])) {
            $config = self::getJsonEnv()[$type];
        } else {
            $config = self::loadConfig($type)->getConfig();
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
            $_ENV['COMPOSER'] = $config;
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
    }

    public static function setHttpConfig(HttpConfig $config)
    {
        parent::setHttpConfig($config);
    }

    public static function setModelConfig(ModelConfig $config)
    {
        parent::setModelConfig($config);
    }

    public static function setSecurityConfig(SecurityConfig $config)
    {
        parent::setSecurityConfig($config);
    }

    public static function setSystemConfig(SystemConfig $config)
    {
        parent::setSystemConfig($config);
    }

    public static function saveConfig(string $type, array $config)
    {
        parent::saveConfig($type, $config);
    }

    public static function mergeConfig(string $type, array $config)
    {
        $currentConfig = self::getConfig($type);
        $newConfig = array_replace_recursive($currentConfig, $config);
        parent::saveConfig($type, $newConfig);
    }

    public static function clearAll()
    {
        if (isset($_ENV)) unset($_ENV);
    }
}
