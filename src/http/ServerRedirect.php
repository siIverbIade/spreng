<?php

namespace Spreng\http;

use Spreng\model\Forms;

/**
 * ServerRedirect
 */
class ServerRedirect extends Forms
{
    public $action;
    public $method;
    public $enctype;
    public $inputs;
    public $errormsg;
    public $panicButton;

    public function __construct(string $errormsg = '', string $action = '', array $inputs = [], string $method = 'GET', string $enctype = '', bool $panicButton = false)
    {
        $this->action = $action == '' ? '' : "action = $action";
        $this->method = $method == '' ? '' : "method = $method";
        $this->enctype = $enctype == '' ? '' : "enctype = $enctype";
        $this->inputs = $inputs;
        $this->errormsg = $errormsg;
        $this->panicButton = $panicButton;

        parent::__construct();
    }
}
