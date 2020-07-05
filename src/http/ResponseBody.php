<?php

declare(strict_types=1);

namespace Spreng\http;

use Spreng\http\HttpResponse;

/**
 * ResponseBody
 */
class ResponseBody extends HttpResponse
{
    private $responseObj;

    public function __construct(callable $callback = null, $url = false, string $method = 'GET', array $permissions = [], array $headers = [])
    {
        array_push($headers, 'Content-Type: application/json');
        parent::__construct($callback, $url, $method, $permissions, $headers);
    }

    public function processResponse()
    {
        $responseCallback = parent::response();
        if ($responseCallback !== null) {
            $this->responseObj = $responseCallback();
        }
    }

    public function encodedResponse(): string
    {
        if ($this->responseObj !== null) {
            return json_encode($this->responseObj, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
        }
        return '';
    }

    public function getObjResponse()
    {
        return $this->responseObj;
    }
}
