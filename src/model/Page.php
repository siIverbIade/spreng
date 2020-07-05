<?php

declare(strict_types=1);

namespace Spreng\model;

use Spreng\model\Fragment;
use Spreng\system\utils\FileUtils;

/**
 * Page
 */
abstract class Page extends Fragment
{
    public $servermsg = '';

    public function __construct()
    {
        $template = FileUtils::fileName(get_called_class());
        parent::__construct($template, 'pages/html');
        $this->build();
    }
}
