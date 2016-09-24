<?php
/**
 * Malang Conf Configuration file
 *
 * @license MIT
 */
return [
    // ENVIRONMENT `development`
    'development' => [
        'database' => [
            'driver'   => 'sqlite',
            'prefix'   => 'prefix',
            'path'     => null,
            /*
            'host'     => 'localhost',
            'username' => 'root',
            'password' => 'mysql',
            'dbname'   => 'dbname',
            */
        ],
        'session'  => [
            'driver'   => 'internal',
            'path'     => '/',
            'name'     => 'conf_dev',
            'lifetime' => null
        ],
        'security' => [
            'hash' => '',
            'salt' => ''
        ]
    ],
    // ENVIRONMENT `production`
    'production' => [
        'database' => [
            'driver'   => 'sqlite',
            'prefix'   => 'prefix',
            'path'     => null,
            /*
            'host'     => 'localhost',
            'username' => 'root',
            'password' => 'mysql',
            'dbname'   => 'dbname',
            */
        ],
        'session'  => [
            'driver'   => 'internal',
            'path'     => '/',
            'name'     => 'conf_prod',
            'lifetime' => null
        ],
        'security' => [
            'hash' => '',
            'salt' => ''
        ]
    ]
];
