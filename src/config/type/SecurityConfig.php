<?php

declare(strict_types=1);

namespace Spreng\config\type;

use Spreng\http\HttpSession;
use Spreng\config\type\Config;

/**
 * SecurityConfig
 */
class SecurityConfig extends Config
{
    private $root_url;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->root_url = HttpSession::rootUrl();
    }

    public function isEnabled(): bool
    {
        return $this->getOneConfig('enabled');
    }

    public static function bCrypt(string $password, int $cost): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    private function getRootUrl(): string
    {
        return $this->root_url ? "/" . basename($this->root_url) : '';
    }

    public function loginUrl(): string
    {
        return $this->getOneConfig('services_url')['login'];
    }

    public function authUrl(): string
    {
        return $this->getOneConfig('services_url')['auth'];
    }

    public function startUrl(): string
    {
        return $this->getOneConfig('services_url')['start'];
    }

    public function logoutUrl(): string
    {
        return $this->getOneConfig('services_url')['logout'];
    }

    public function loginFullUrl(): string
    {
        return $this->getRootUrl() . $this->getOneConfig('services_url')['login'];
    }

    public function authFullUrl(): string
    {
        return $this->getRootUrl() . $this->getOneConfig('services_url')['auth'];
    }

    public function startFullUrl(): string
    {
        return $this->getRootUrl() . $this->getOneConfig('services_url')['start'];
    }

    public function logoutFullUrl(): string
    {
        return $this->getRootUrl() . $this->getOneConfig('services_url')['logout'];
    }

    public function getCustomPermissionsList(): array
    {
        return $this->getOneConfig('custom_permissions');
    }

    public function getTables(): array
    {
        return $this->getOneConfig('tables');
    }

    public function getDefaultUser(): array
    {
        return $this->getOneConfig('default_admin_user');
    }

    public function getJwt(): array
    {
        return $this->getOneConfig('jwt');
    }

    public function jwtSecretKey(): string
    {
        return $this->getJwt()['secret_key'];
    }

    public function jwtPayload(): array
    {
        $defaultPayload = $this->getDefaultUser();
        $jwtPayload = $this->getJwt()['payload'];
        $defaultPayload = array_merge($defaultPayload, array_diff($jwtPayload, $defaultPayload));
        return $defaultPayload;
    }
}
