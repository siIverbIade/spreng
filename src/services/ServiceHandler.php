<?php

namespace Spreng\services;

use Spreng\config\GlobalConfig;
use Spreng\system\collections\ServiceList;

/**
 * ServiceHandler
 */
class ServiceHandler
{
    private $services;
    private $classes;

    public function __construct()
    {
        $this->services = new ServiceList();
        $this->classes = GlobalConfig::getAllImplementationsOf(GlobalConfig::getSystemConfig()->getServicePath(), 'Spreng\services\Service');
        $this->registerProcesses();
    }

    private function registerProcesses()
    {
        foreach ($this->classes as $class => $parentClass) {
            $this->services->add(new $class);
        }
    }

    public function run()
    {
        foreach ($this->services->getAll() as $prc) {
            $prcShifted = $prc->getFn();
            foreach ($prcShifted as $name) {
                $prc->{$name}();
            }
        }
    }
}
