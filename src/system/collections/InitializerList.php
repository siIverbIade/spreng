<?php

declare(strict_types=1);

namespace Spreng\system\collections;

use Spreng\system\boot\Initializer;

/**
 * InitializerList
 */
class InitializerList
{
    private $initializers = [];

    public function __construct($initializers = [])
    {
        $this->initializers = $initializers;
    }

    public function add(Initializer $initializer)
    {
        $this->initializers[] = $initializer;
    }

    public function getAll()
    {
        return $this->initializers;
    }
}
