<?php

namespace Spreng\security;

use Exception;
use Spreng\security\JwtFb;
use Spreng\system\log\Logger;
use Spreng\config\GlobalConfig;

/**
 * SessionUser
 */
class SessionUser
{
    private $jwtToken;
    private $payload;

    public function __construct($payload = null, bool $remember = false)
    {
        if ($payload == null) {
            $this->jwtToken = self::getSessionToken();
        } else {
            $jwt = new JwtFb(GlobalConfig::getSecurityConfig()->jwtSecretKey());
            $this->payload = $payload;
            $this->jwtToken = $jwt->getToken($payload);
            self::setSessionToken($this->jwtToken, $remember);
        }
    }

    public static function getSessionToken(): string
    {
        return isset($_COOKIE['usertoken']) ? $_COOKIE['usertoken'] : '';
    }

    private static function setSessionToken(string $token, bool $remember)
    {
        setcookie("usertoken", $token, $remember ? time() + 3600 * 24 * 30 : 0, '/', '', false, true);
    }

    public static function getCredentials(string $jwtToken)
    {
        $jwt = new JwtFb(GlobalConfig::getSecurityConfig()->jwtSecretKey());

        try {
            $userCredentials = $jwt->getDecoded($jwtToken);
        } catch (Exception $e) {
            Logger::warning($e->getMessage());
        }

        return  isset($userCredentials) ? $userCredentials : null;
    }

    public function getJsonPayload()
    {
        return json_encode($this->payload, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
    }

    public static function getSessionCredentials()
    {
        if (!isset($_COOKIE['usertoken'])) return null;
        return self::getCredentials($_COOKIE['usertoken']);
    }

    public static function getUserName(): string
    {
        $credentials = self::getSessionCredentials();
        return ($credentials == null) ? '' : $credentials->user;
    }

    public static function getUserPassword(): string
    {
        $credentials = self::getSessionCredentials();
        return ($credentials == null) ? '' : $credentials->key;
    }

    public static function getUserRemember(): bool
    {
        $credentials = self::getSessionCredentials();
        return ($credentials == null) ? '' : $credentials->remember;
    }

    public static function getUserDetails(): string
    {
        $credentials = self::getSessionCredentials();
        return ($credentials == null) ? '' : json_encode($credentials, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE);
    }

    public static function getUserGroup(): string
    {
        $credentials = self::getSessionCredentials();
        return ($credentials == null) ? '' : $credentials->group;
    }

    public static function getUserPermissions(): array
    {
        $credentials = self::getSessionCredentials();
        return ($credentials == null) ? [] : $credentials->permissions;
    }

    public static function clearCredentials()
    {
        unset($_COOKIE['usertoken']);
        setcookie('usertoken', null, -1);
    }
}
