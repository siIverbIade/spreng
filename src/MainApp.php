<?php

declare(strict_types=1);

namespace Spreng;

use Spreng\http\HttpSession;
use Spreng\http\RequestHandler;
use Spreng\services\ServiceHandler;
use Spreng\system\boot\InitializeHandler;

/**
 * MainApp
 */
abstract class MainApp
{
    public static function init()
    {
        (new InitializeHandler($_SERVER['DOCUMENT_ROOT']))->run();
        (new ServiceHandler)->run();
        $session = new HttpSession();
        (new RequestHandler($session))->processRequest();
    }
}
