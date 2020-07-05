<?php

namespace Spreng\system\boot;

use Exception;
use Spreng\config\GlobalConfig;
use Spreng\system\utils\FileUtils;
use Spreng\system\collections\InitializerList;

/**
 * InitializeHandler
 */
class InitializeHandler
{
    private $initializers;
    private $classes;

    public function __construct(string $documentRoot)
    {
        $sc = GlobalConfig::getSystemConfig();
        $firstRun = $sc->getFirstRun();
        if ($firstRun) {

            $htaccess = dirname(__DIR__, 2) . '/config/resources/.htaccess';

            if (!file_exists($documentRoot . '/.htaccess')) {
                try {
                    copy($htaccess, $documentRoot . '/.htaccess');
                } catch (Exception $e) {
                    echo "Spreng could not copy file '$htaccess' to the root folder '$documentRoot'. Please, do it manually.";
                    exit;
                }
            } else {
                if (!FileUtils::compare($htaccess, $documentRoot . '/.htaccess')) {
                    echo "Apache's .htaccess found in rootDir '$documentRoot' is not suited for this application. Please delete it and let spreng create a new one for you";
                    exit;
                }
            }
            $sc->setSourcePath($documentRoot . '/src');
            $sc->setFirstRun(false);
            GlobalConfig::setSystemConfig($sc);
        }

        if ($sc->getSourcePath() == null | $sc->getSourcePath() == '') {
            echo "Read configuration failed in setup.json: 'source_path' is not defined.";
            exit;
        }

        if (!file_exists($sc->getSourcePath())) {
            echo "Read configuration failed in setup.json: 'source_path' was not found.";
            exit;
        }

        if ($firstRun) {
            $this->initializers = new InitializerList();
            $this->classes = GlobalConfig::getAllImplementationsOf(GlobalConfig::getSystemConfig()->getServicePath(), Initializer::class);
            $this->registerProcesses();
        }
    }

    private function registerProcesses()
    {
        foreach ($this->classes as $class => $parentClass) {
            $this->initializers->add(new $class);
        }
    }

    public function run()
    {
        if (isset($this->initializers)) {
            foreach ($this->initializers->getAll() as $prc) {
                $prcShifted = $prc->getFn();
                foreach ($prcShifted as $name) {
                    $prc->{$name}();
                }
            }
        }
    }
}
