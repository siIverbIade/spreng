<?php

declare(strict_types=1);

namespace Spreng\http;

use Spreng\system\Server;

/**
 * HttpSession
 */
class HttpSession
{
    private $urlParse;

    public function __construct()
    {
        $requestUri = Server::getRequestUri();
        $this->urlParse = parse_url($requestUri);
        $GLOBALS['httpcode'] = 0;
        $GLOBALS['redirect'] = '';
    }

    public static function initSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function name(string $varName, string $ifNull = ''): string
    {
        if (isset($_GET[$varName])) {
            return $_GET[$varName];
        } elseif (isset($_POST[$varName])) {
            return $_POST[$varName];
        } else {
            return $ifNull;
        }
    }

    public static function files(string $varName = ''): array
    {
        if (isset($_FILES[$varName])) {
            if ($varName == '') {
                return $_FILES;
            } else {
                return $_FILES[$varName];
            }
        } else {
            return [];
        }
    }

    public static function rootUrl(): string
    {
        //erro quando servidor é na raiz, ajustar para $_SERVER['REQUEST_URI'])[0]
        return explode('/', $_SERVER['REQUEST_URI'])[0];
    }

    public static function fullUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function rootRequest()
    {
        return $this->urlParse['path'];
    }

    public function urlParameters(): array
    {
        $params = [];
        if (isset($this->urlParse['query'])) parse_str($this->urlParse['query'], $params);
        return $params;
    }

    public function urlParameter(string $name, string $ifNull = ''): string
    {
        if (isset($this->urlParameters()[$name])) {
            return $this->urlParameters()[$name];
        } else {
            return $ifNull;
        }
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function body()
    {
        return file_get_contents("php://input");
    }

    public static function username()
    {
        return isset($_POST['username']) ? $_POST['username'] : false;
    }

    public static function password()
    {
        return isset($_POST['password']) ? $_POST['password'] : false;
    }

    public static function remember(): bool
    {
        return isset($_POST['remember']) ? (bool) $_POST['remember'] : false;
    }

    public static function clientIp()
    {
        return $_ENV['REMOTE_ADDR'];
    }

    public static function postData(string $url, array $data = [], bool $follow = false)
    {
        $cURLConnection = curl_init($url);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
        curl_setopt($cURLConnection, CURLOPT_FOLLOWLOCATION, $follow);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        return $apiResponse;
    }

    public static function throwHttpCode(int $httpCode): callable
    {
        return function () use ($httpCode) {
            $GLOBALS['httpcode'] = $httpCode;
        };
    }

    public static function echoRedirect(string $redirectUrl): callable
    {
        return function () use ($redirectUrl) {
            $GLOBALS['redirect'] = $redirectUrl;
        };
    }

    public static function getVar(string $name)
    {
        isset($GLOBALS[$name]) ? $GLOBALS[$name] : '';
    }

    public static function setVar(string $name, $arg)
    {
        $GLOBALS[$name] = $arg;
    }

    public static function markUp(string $name, int $duration = 0): string
    {
        if (!isset($_COOKIE[$name])) {
            $now = time();
            setcookie($name, date('d/m/Y \à\s H:i:s', $now), $duration == 0 ? 0 : $now + $duration);
            $GLOBALS[$name] = true;
            return '';
        } else {
            $GLOBALS[$name] = false;
            return $_COOKIE[$name];
        }
    }

    public static  function clear()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
    }
}
