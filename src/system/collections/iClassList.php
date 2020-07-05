<?php

namespace Spreng\system\collections;

use Spreng\system\loader\iSpreng;

interface iClassList
{
    public function add(iSpreng $obj);
    public function getAll();
}
