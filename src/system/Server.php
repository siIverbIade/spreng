<?php

namespace Spreng\system;

/**
 * Server
 */
class Server
{
    public static function getTime()
    {
        return $_SERVER['REQUEST_TIME'];
    }

    public static function getDocumentRoot()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }
}
