<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.tech
 */

namespace madong\admin\services\scheduler\event;

use app\common\enum\system\OperationResult;

class UrlTask implements EventBootstrap
{
    /**
     * @param $crontab
     *
     * @return array
     */
    public static function parse($crontab): array
    {
        $url  = trim($crontab['target'] ?? '');
        $code = OperationResult::SUCCESS->value;
        try {
            $client   = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $log      = strip_tags($response->getBody()->getContents());
        } catch (\Throwable $throwable) {
            $code = OperationResult::FAILURE->value;
            $log  = $throwable->getMessage();
        }
        return ['code' => $code, 'log' => $log];
    }
}
