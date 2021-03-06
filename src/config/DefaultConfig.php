<?php

namespace Spreng\config;

/**
 * DefaultConfig
 */
class DefaultConfig
{
    private static $config = [
        "connection" => [
            "auth" => [
                "url" => "localhost",
                "port" => "3306",
                "database" => "database_auth",
                "user" => "root",
                "password" => "",
                "regenerate" => true
            ],
            "system" => [
                "url" => "localhost",
                "port" => "3306",
                "database" => "database_system",
                "user" => "root",
                "password" => "",
                "regenerate" => true
            ]
        ],
        "http" => [
            "listen_port" => "80",
            "controllers_path" => ""
        ],
        "model" => [
            "models_path" => "",
            "templates_root" => "resources/templates",
            "assets_folder" => "resources/templates/assets",
            "auto_reload" => true
        ],
        "orm" => [
            "entities_path" => "",
            "repository_path" => ""
        ],
        "security" => [
            "enabled" => false,
            "services_url" => [
                "login" => "/login",
                "auth" => "/auth/access",
                "start" => "/home",
                "logout" => "/logout"
            ],
            "tables" => [
                "user" => "user",
                "group" => "group",
                "permission" => "permission"
            ],
            "default_admin_user" => [
                "username" => "admin",
                "password" => "admin",
                "name" => "System Administrator"
            ],
            "jwt" => [
                "secret_key" => "my_jwt_security_key",
                "payload" => [
                    "username" => "username",
                    "password" => "password",
                    "name" => "name",
                    "datetime" => "datetime",
                    "permissions" => "permissions"
                ]
            ],
            "custom_permissions" => [
                "USER",
                "ADMIN"
            ]
        ],
        "system" => [
            "first_run" => true,
            "intro" => true,
            "services_path" => "",
            "log_file" => "syslog/info.log",
            "autoloader" => [
                "source_class" => "",
                "source_path" => ""
            ]
        ]
    ];

    public static function config(): array
    {
        return self::$config;
    }
}
