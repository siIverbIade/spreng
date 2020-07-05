<?php

declare(strict_types=1);

namespace Spreng\http;

/**
 * Resolver
 */
class Resolver
{
    public $echo;
    public $httpCode;
    public $headers;

    public function __construct(string $echo, int $httpCode, array $headers = [])
    {
        $this->echo = $echo;
        $this->httpCode = $httpCode;
        $this->headers = $headers;
    }
}
