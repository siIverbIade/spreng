<?php

declare(strict_types=1);

namespace Spreng\system\collections;

use Spreng\http\Controller;

/**
 * ControllerList
 */
class ControllerList
{
    private $controllers = [];

    public function __construct($controllers = [])
    {
        $this->controllers = $controllers;
    }

    public function add(Controller $controller)
    {
        $this->controllers[] = $controller;
    }

    public function getAll()
    {
        return $this->controllers;
    }
}
