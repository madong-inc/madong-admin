<?php

use madong\services\upload\storage\Cos;
use madong\services\upload\storage\Local;
use madong\services\upload\storage\Oss;
use madong\services\upload\storage\Qiniu;
use madong\services\upload\storage\S3;

return [
    'debug'           => config('app.debug'),
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
