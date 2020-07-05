<?php

declare(strict_types=1);

namespace Spreng\security;

use Spreng\model\Page;

/**
 * SecurityLogin
 */
class SecurityLogin extends Page
{
    public $username;
    public $auth_url;
    public $remember;
}
