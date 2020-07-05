<?php

declare(strict_types=1);

namespace Spreng\connection;

use Spreng\config\GlobalConfig;
use Spreng\connection\Connection;
use Spreng\config\type\SecurityConfig;

/**
 * AuthConnPool
 */
class AuthConnPool extends Connection
{
    public function __construct(string $pool = 'auth')
    {
        parent::__construct($pool);
        if ($this->allowRegenerate()) self::createAdmin();
    }

    public static function createAdmin()
    {
        $secConf = GlobalConfig::getSecurityConfig();
        $customPermissions = $secConf->getCustomPermissionsList();
        $jwtConfig = $secConf->jwtPayload();
        $tableConfig = $secConf->getTables();
        $defaultUser = $secConf->getDefaultUser();

        $defaultPassword = SecurityConfig::bCrypt($defaultUser['password'], 13);
        $ADMIN = self::findOne($tableConfig['user'], ' BINARY ' . $jwtConfig['username'] . ' = ? ', [$defaultUser['username']]);

        if ($ADMIN == null) {
            self::wipe($tableConfig['permission']);
            self::wipe($tableConfig['group']);

            //cria as permissões Listadas em $customPermissions
            $MASTERACCESS = self::dispense($tableConfig['permission']);
            $MASTERACCESS->role = 'MASTER';
            foreach ($customPermissions as $permission) {
                $BEAN = self::dispense($tableConfig['permission']);
                $BEAN->role = $permission;
                $PERMISSIONSBEAN[] = $BEAN;
            }

            //cria o grupo ADMIN com permissões MASTER e USER
            $MASTERGROUP = self::dispense($tableConfig['group']);
            $MASTERGROUP->name = 'ADMIN';
            $MASTERGROUP->sharedPermissaoList[] = $MASTERACCESS;

            //cria o usuário ADMIN no grupo MASTER, com login/senha salvos em 'default_user'
            $NEWADMIN = self::dispense($tableConfig['user']);
            $NEWADMIN->{$jwtConfig['username']} = $defaultUser['username'];
            $NEWADMIN->{$jwtConfig['name']} = $defaultUser['name'];
            $NEWADMIN->{$jwtConfig['password']} = $defaultPassword;
            $NEWADMIN->{$tableConfig['group']} = $MASTERGROUP;

            $BEANS = [$NEWADMIN, $MASTERGROUP];

            self::storeAll($PERMISSIONSBEAN);
            self::storeAll($BEANS);
        } elseif (!$ADMIN[$jwtConfig['password']] == $defaultPassword) $ADMIN[$jwtConfig['password']] = $defaultPassword;
    }
}
