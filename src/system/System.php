<?php

namespace Spreng\system;

/**
 * System
 */
class System
{
    public static function Message(): string
    {
        return isset($_SESSION['system']['message']) ? $_SESSION['system']['message'] : '';
    }

    public static function setMessage(string $msg)
    {
        $_SESSION['system']['message'] = $msg;
    }

    public static function throwMessage(string $msg)
    {
        $_SESSION['system']['message'] = $msg;
        header('Location : ./alert');
        die();
    }
}
