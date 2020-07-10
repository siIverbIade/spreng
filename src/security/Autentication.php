<?php

namespace Spreng\security;

use Spreng\http\HttpSession;
use Spreng\system\log\Logger;
use Spreng\config\GlobalConfig;
use Spreng\security\SessionUser;
use Spreng\connection\Connection;

/**
 * Autentication
 */
class Autentication
{
    private $secConf;
    private $session;
    private $credentials;

    public function __construct(HttpSession $session)
    {
        $this->credentials = SessionUser::getSessionCredentials();
        $this->secConf = GlobalConfig::getSecurityConfig();
        $this->session = $session;
    }

    public function handleAuth(array $accessPointPermissions): bool
    {
        if (!$this->secConf->isEnabled()) return true;
        $userPermissions = ($this->credentials == null) ? [] : $this->credentials->permissions;
        if (in_array('MASTER', $userPermissions) | in_array('DEFAULT', $accessPointPermissions) | array_intersect($userPermissions, $accessPointPermissions) !== []) return true;
        else return false;
    }

    public function getUserCredentials()
    {
        if ($this->secConf->isEnabled())
            return $this->credentials;
        else
            return [null];
    }

    public function try(Connection $conn): bool
    {
        if (!$this->secConf->isEnabled()) return true;

        self::setAuthMessage('');
        $userCredentials = $this->getUserCredentials();

        if (!$userCredentials == null) {
            if ($userCredentials->ip !== $this->session::clientIp()) {
                self::setAuthMessage('IP não autorizado!');
                SessionUser::clearCredentials();
                return false;
            }
            $username = SessionUser::getUserName();
            $password = SessionUser::getUserPassword();
            $rememberMe =  SessionUser::getUserRemember();
        } else {
            $username = $this->session::username();
            $password = $this->session::password();
            $rememberMe = $this->session::remember();
        }

        if ($username == '') {
            self::setAuthMessage('Digite o nome de Usuário');
            SessionUser::clearCredentials();
            return false;
        }

        if ($password == '') {
            self::setAuthMessage('Digite a senha');
            SessionUser::clearCredentials();
            return false;
        }

        $jwtConfig = $this->secConf->jwtPayload();
        $tableConfig = $this->secConf->getTables();

        $userTable = $tableConfig['user'];
        $groupTable = $tableConfig['group'];
        $permissionTable = $tableConfig['permission'];
        $user = $conn::findOne($userTable, ' BINARY ' . $jwtConfig['username'] . ' = ? ', [$username]);

        if ($user == null) {
            self::setAuthMessage('Usuário não existe!');
            SessionUser::clearCredentials();
            return false;
        }

        if (!$userCredentials == null) {
            if (!password_verify($user[$jwtConfig['password']], $password)) {
                self::setAuthMessage('Credenciais Expiradas!');
                SessionUser::clearCredentials();
                return false;
            }
        } else {
            if (!password_verify($password, $user[$jwtConfig['password']])) {
                self::setAuthMessage('Credenciais Inválidas!');
                SessionUser::clearCredentials();
                return false;
            }
        }

        if (!$user == null) $group_permission = (array) $conn::getAll("SELECT `$groupTable`.name, `$permissionTable`.role 
        FROM `$userTable` INNER JOIN (`$groupTable` INNER JOIN ({$groupTable}_$permissionTable INNER JOIN `$permissionTable` 
        ON {$groupTable}_$permissionTable.{$permissionTable}_id = $permissionTable.id) 
        ON `$groupTable`.id = {$groupTable}_$permissionTable.{$groupTable}_id) 
        ON `$userTable`.{$groupTable}_id = `{$groupTable}`.id 
        WHERE `$userTable`.id=?", [$user->id]);

        $group = $group_permission[0]['name'];

        $permissions = array_map(function ($gp) {
            return $gp['role'];
        }, $group_permission);

        $time = time();

        $payload = [
            'user' => $user->{$jwtConfig['username']},
            'key' =>  $this->secConf::bCrypt($user->{$jwtConfig['password']}, 10),
            'group' => $group,
            'permissions' => $permissions,
            'remember' => $rememberMe,
            'iat' => $time,
            'exp' => $time + 3600 * 24 * 30,
            'ip' => $this->session::clientIp()
        ];

        new SessionUser($payload, $rememberMe);
        Logger::info("User $username has logged in - IP: " . $this->session::clientIp());
        return true;
    }

    public function sessionUser(): SessionUser
    {
        return new SessionUser;
    }

    public static function getAuthMessage()
    {
        return isset($_ENV['authmessage']) ? $_ENV['authmessage'] : '';
    }

    public static function setAuthMessage(string $message)
    {
        $_ENV['authmessage'] = $message;
    }
}
