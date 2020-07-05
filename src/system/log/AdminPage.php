<?php

declare(strict_types=1);

namespace Spreng\system\log;

use Spreng\model\Forms;

/**
 * AdminPage
 */
class AdminPage extends Forms
{
    public $refreshRate;
    public $logContent;

    public function build()
    {
        $this->logContent = Logger::getLogs();
    }
}
