<?php

return [
    'default'   => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],
    'namespace' => 'ma-dong',
    'prefix'    => '',
    'type'      => 'redis',//redis||file
    'store'     => [
        //更多
        'redis' => [],
        'file'  => [],
    ],
];
