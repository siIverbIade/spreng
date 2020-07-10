<?php

declare(strict_types=1);

namespace Spreng\config;

use Exception;
use Spreng\system\Server;
use Spreng\system\files\Json;
use Spreng\config\type\Config;
use Spreng\config\type\HttpConfig;
use Spreng\system\utils\FileUtils;
use Spreng\config\type\ModelConfig;
use Spreng\config\type\SystemConfig;
use Spreng\config\type\ComposerConfig;
use Spreng\config\type\SecurityConfig;
use Spreng\config\type\ConnectionConfig;

class ParseConfig
{
    public static function global(bool $process = false): Json
    {
        $ApplicationSetup = Server::getDocumentRoot() . '/application.json';
        try {
            $json = new Json($ApplicationSetup);
        } catch (Exception $e) {
            FileUtils::save($ApplicationSetup);
            $json = new Json($ApplicationSetup);
            $json->schemaJSON = DefaultConfig::config();
            $json->writeSchemaJSON();
        }
        if ($process) $json->process();
        return $json;
    }

    private static function composer(bool $process = false): Json
    {
        $ComposerFile = Server::getDocumentRoot() . '/composer.json';
        try {
            $json = new Json($ComposerFile);
        } catch (Exception $e) {
            echo " Check if autoload psr-4 property is set in your project.";
            exit;
        }
        if ($process) $json->process();
        return $json;
    }

    private static function cfgTypeClass(string $type): string
    {
        return 'Spreng\config\type\\' . ucfirst($type) . 'Config';
    }

    protected static function loadConfig(string $type): Config
    {
        $cfgType = self::cfgTypeClass($type);
        $configObj = new $cfgType;

        $json = self::global();

        if (!$json == null) {
            $json->process();
            $global = $json->schemaJSON;
        }

        $defaults = DefaultConfig::config();

        $configObj->assets($defaults[$type], isset($global[$type]) ? $global[$type] : $defaults[$type]);

        return $configObj;
    }

    protected static function autoLoad(): ComposerConfig
    {
        $configObj = new ComposerConfig;

        $json = self::composer();

        if (!$json == null) {
            $json->process();
            $global = $json->schemaJSON;
        }

        $configObj->assets($global, $global);

        return $configObj;
    }

    public static function getConnectionConfig(): ConnectionConfig
    {
        return self::loadConfig('connection');
    }

    public static function getHttpConfig(): HttpConfig
    {
        return self::loadConfig('http');
    }

    public static function getModelConfig(): ModelConfig
    {
        return self::loadConfig('model');
    }

    public static function getSecurityConfig(): SecurityConfig
    {
        return self::loadConfig('security');
    }

    public static function getSystemConfig(): SystemConfig
    {
        return self::loadConfig('system');
    }

    public static function setConnectionConfig(ConnectionConfig $connectionConfig)
    {
        self::saveConfig('connection', $connectionConfig->getConfig());
    }

    public static function setHttpConfig(HttpConfig $httpConfig)
    {
        self::saveConfig('http', $httpConfig->getConfig());
    }

    public static function setModelConfig(ModelConfig $modelConfig)
    {
        self::saveConfig('model', $modelConfig->getConfig());
    }

    public static function setSecurityConfig(SecurityConfig $securityConfig)
    {
        self::saveConfig('security', $securityConfig->getConfig());
    }

    public static function setSystemConfig(SystemConfig $systemConfig)
    {
        self::saveConfig('system', $systemConfig->getConfig());
    }

    public static function saveConfig(string $type, array $config)
    {
        $global = self::global();
        $global->process();
        $global->schemaJSON[$type] = $config;
        $global->writeSchemaJSON();
    }

    public static function saveAll(array $config)
    {
        $global = self::global();
        $global->process();
        $global->schemaJSON = $config;
        $global->writeSchemaJSON();
    }
}
