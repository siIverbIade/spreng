<?php

declare(strict_types=1);

namespace Spreng\http;

use Spreng\http\HttpResponse;

/**
 * ModelAndView
 */
class ModelAndView extends HttpResponse
{
    private $responseObj;

    public function __construct(callable $callback = null, $url = false, string $method = 'GET', array $permissions = [], array $headers = [])
    {
        array_push($headers, 'Content-Type: text/html');
        parent::__construct($callback, $url, $method, $permissions, $headers);
    }

    public function processResponse()
    {
        $responseCallback = parent::response();
        if ($responseCallback !== null) {
            $this->responseObj = $responseCallback();
        }
    }

    public function render()
    {
        return ($this->responseObj == null) ? '' : $this->responseObj->show();
    }

    public function getObjResponse()
    {
        return $this->responseObj;
    }
}
