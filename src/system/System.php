<?php

namespace Spreng\system;

/**
 * System
 */
class System
{
    public static function Message(): string
    {
        return isset($_ENV['system_message']) ? $_ENV['system_message'] : '';
    }

    public static function setMessage(string $msg)
    {
        $_ENV['system_message'] = $msg;
    }

    public static function throwMessage(string $msg)
    {
        $_ENV['system_message'] = $msg;
        header('Location : ./alert');
        die();
    }
}
