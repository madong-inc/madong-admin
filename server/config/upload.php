<?php

use madong\admin\services\upload\storage\Cos;
use madong\admin\services\upload\storage\Local;
use madong\admin\services\upload\storage\Oss;
use madong\admin\services\upload\storage\Qiniu;
use madong\admin\services\upload\storage\S3;

return [
    'debug'           => config('app.debug'),
    'cdn_url'         => '',
    'cdn_url_params'  => '',
    'default_avatar'  => '/upload/avatar.jpeg',
    'config_key'      => [
        'local' => '',
        'oss'   => '',
        'cos'   => '',
        'qiniu' => '',
        's3'    => '',
    ],
    'adapter_classes' => [
        'local' => Local::class,
        'oss'   => Oss::class,
        'cos'   => Cos::class,
        'qiniu' => Qiniu::class,
        's3'    => S3::class,
    ],
];
