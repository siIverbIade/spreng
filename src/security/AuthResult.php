<?php

namespace Spreng\security;

class AuthResult
{
    private $auth = false;
    private $authMessage = '';

    public function __construct(bool $auth = false, string $authMessage = '')
    {
        $this->isAuth = $auth;
        $this->authMessage = $authMessage;
    }

    public function isAuth(): bool
    {
        return $this->auth;
    }

    public function getAuthMessage(): string
    {
        return $this->authMessage;
    }
}
