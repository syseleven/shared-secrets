<?php

define("SYS11_SECRETS", true);
define("ROOT_DIR", __DIR__);

# include required configuration
if ( file_exists( __DIR__ . '/config/config.php' ) ) {
    require_once( __DIR__ . '/config/config.php' );
}
else {
    require_once( __DIR__ . '/config/config.env.php' );
}

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations'
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
