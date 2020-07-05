<?php

declare(strict_types=1);

namespace Spreng\config\type;

use Spreng\config\type\Config;

/**
 * ModelConfig
 */
class ModelConfig extends Config
{
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getModelsPath()
    {
        return $this->getOneConfig('models_path');
    }

    public function setModelsPath(string $val)
    {
        $this->setOneConfig('models_path', $val);
    }

    public function getTemplateRoot()
    {
        return $this->getOneConfig('templates_root');
    }

    public function setTemplateRoot(string $val)
    {
        $this->setOneConfig('templates_root', $val);
    }

    public function getAssetsFolder()
    {
        return $this->getOneConfig('assets_folder');
    }

    public function setAssetsFolder(string $val)
    {
        $this->setOneConfig('assets_folder', $val);
    }

    public function isAutoReloadEnabled(): bool
    {
        return $this->getOneConfig('auto_reload');
    }

    public function disableAutoReload()
    {
        $this->setOneConfig('auto_reload', true);
    }

    public function enableAutoReload()
    {
        $this->setOneConfig('auto_reload', false);
    }
}
