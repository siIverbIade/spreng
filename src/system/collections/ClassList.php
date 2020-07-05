<?php

declare(strict_types=1);

namespace Spreng\system\collections;

use Spreng\system\collections\iClassList;
use Spreng\system\loader\iSpreng;
use TypeError;

/**
 * ClassList
 */
class ClassList implements iClassList
{
    private $list = [];
    private $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function add(iSpreng  $element)
    {
        if (!($element instanceof $this->class)) throw new TypeError(get_class($element) . ' is not an instance of ', $this->class);
        $this->list[] = $element;
    }

    public function getAll()
    {
        return $this->list;
    }
}
