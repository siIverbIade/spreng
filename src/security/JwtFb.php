<?php

namespace Spreng\security;

use Firebase\JWT\JWT;

/**
 * JwtFb
 */
class JwtFb extends JWT
{
    private $secretkey;

    public function __construct(string $secretkey)
    {
        $this->secretkey = $secretkey;
    }

    public function getToken($payload): string
    {
        $jwt = self::encode($payload, $this->secretkey);
        return $jwt;
    }

    public function getDecoded(string $jwt): object
    {
        $decoded = self::decode($jwt, $this->secretkey, array('HS256'));
        return $decoded;
    }
}
