<?php

declare(strict_types=1);

namespace Spreng\http;

/**
 * HttpResponse
 */
class HttpResponse
{
    private $url;
    private $method;
    private $response;
    private $headers;
    private $permissions;

    public function __construct(callable $callback = null, $url = false, string $method = 'GET', array $permissions = [], array $headers = [])
    {
        $this->response = $callback;
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->permissions = $permissions;
    }

    public function url()
    {
        return $this->url;
    }

    public function method()
    {
        return $this->method;
    }

    public function response()
    {
        return $this->response;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function permissions(): array
    {
        return $this->permissions;
    }

    public function httpcode(): int
    {
        return $GLOBALS['httpcode'];
    }

    public function redirectUrl(): string
    {
        return $GLOBALS['redirect'];
    }
}
