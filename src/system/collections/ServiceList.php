<?php

declare(strict_types=1);

namespace Spreng\system\collections;

use Spreng\services\Service;

/**
 * ServiceList
 */
class ServiceList
{
    private $services = [];

    public function __construct($services = [])
    {
        $this->services = $services;
    }

    public function add(Service $service)
    {
        $this->services[] = $service;
    }

    public function getAll()
    {
        return $this->services;
    }
}
