<?php

declare(strict_types=1);

namespace Spreng\model;

use Spreng\system\utils\FileUtils;

/**
 * Component
 */
abstract class Component extends Fragment
{
    public function __construct()
    {
        $template = FileUtils::fileName(get_called_class());
        parent::__construct($template, 'components/html');
        $this->build();
    }
}
