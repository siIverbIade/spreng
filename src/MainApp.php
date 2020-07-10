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
    public static function init(bool $prodEnv = false)
    {
        (new InitializeHandler($prodEnv))->run();
        (new ServiceHandler)->run();
        (new RequestHandler(new HttpSession))->processRequest();
    }
}
