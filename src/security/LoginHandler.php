<?php

namespace Spreng\security;

use Spreng\config\GlobalConfig;
use Spreng\system\loader\ClassHandler;
use Spreng\system\log\Logger;

/**
 * LoginHandler
 */
class LoginHandler extends ClassHandler
{
    private $loginModel;

    public function __construct()
    {
        $modelsPath = GlobalConfig::getModelConfig()->getModelsPath();
        parent::__construct(SecurityLogin::class, $modelsPath);
        if (count($this->getClassList()->getAll()) == 0) {
            echo 'No SecurityLogin class child was found at ', $modelsPath;
            exit;
        } elseif (count($this->getClassList()->getAll()) > 1) {
            Logger::console_log('WARNING: There are more than one SecurityLogin implementations found at ' . $modelsPath);
        }
        $this->loginModel = $this->getClassList()->getAll()[0];
    }

    public function getLoginModel(): SecurityLogin
    {
        return $this->loginModel;
    }
}
