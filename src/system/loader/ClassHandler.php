<?php

namespace Spreng\system\loader;

use ReflectionClass;
use Spreng\config\GlobalConfig;
use Spreng\system\collections\ClassList;

/**
 * ClassHandler
 */
class ClassHandler
{
    private $classList;
    private $classes;

    public function __construct(string $class, string $folderPath)
    {
        $this->classList = new ClassList($class);
        $this->classes = GlobalConfig::getAllImplementationsOf($folderPath, $class);
        $this->registerAll();
    }

    public function getClassList()
    {
        return $this->classList;
    }

    private function registerAll()
    {
        foreach ($this->classes as $class => $parentClass) {
            $this->classList->add(new $class);
        }
    }

    public function run(callable $hook = null)
    {
        foreach ($this->classList->getAll() as $fn) {
            $fnShifted = $fn->getFn();
            foreach ($fnShifted as $name) {
                if ($hook == null)
                    $fn->{$name}();
                else {
                    $hook($fn, $name);
                }
            }
        }
    }

    public static function getFunctionArgsTypes(string $class, string $method)
    {
        //Target our class
        $args = [];
        $reflector = new ReflectionClass($class);

        //Get the parameters of a method
        $parameters = $reflector->getMethod($method)->getParameters();

        //Loop through each parameter and get the type
        foreach ($parameters as $param) {
            //Before you call getClass() that class must be defined!
            $args[] = $param->getClass()->name;
        }
        return $args;
    }
}
