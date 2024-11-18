<?php

return [
    'default'   => config('cache.redis'),
    'namespace' => 'ma-dong',
    'prefix'    => '',
    'type'      => 'redis',//redis||file
    'redis'     => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],
];
