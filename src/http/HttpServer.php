<?php

namespace Spreng\http;

/**
 * HttpServer
 */
class HttpServer
{
    public static function getTime()
    {
        return $_SERVER['REQUEST_TIME'];
    }
}
