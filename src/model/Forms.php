<?php

declare(strict_types=1);

namespace Spreng\model;

use Spreng\model\Fragment;
use Spreng\system\utils\FileUtils;

/**
 * Forms
 */
abstract class Forms extends Fragment
{
    public $servermsg = '';

    public function __construct()
    {
        $template = FileUtils::fileName(get_called_class());
        parent::__construct($template, 'forms', __DIR__);
        $this->build();
    }
}
