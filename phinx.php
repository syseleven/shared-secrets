<?php

define("SYS11_SECRETS", true);
require_once( __DIR__ . "/config/config.php" );


return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => MYSQL_HOST,
            'name' => MYSQL_DB,
            'user' => MYSQL_USER,
            'pass' => MYSQL_PASS,
            'port' => MYSQL_PORT,
            'charset' => 'utf8'
        ]
    ],
    'version_order' => 'creation'
];
