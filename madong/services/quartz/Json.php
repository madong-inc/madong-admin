<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */
namespace madong\services\quartz;

use support\Response;

class Json
{
    private static int $code = 200;

    public static function make(int $status, string $msg, ?array $data = null, ?array $replace = []): Response
    {
        $res = compact('status', 'msg');

        if (!is_null($data))
            $res['data'] = $data;

        if (is_numeric($res['msg'])) {
            $res['code'] = $res['msg'];
//            $res['msg']  = getLang($res['msg'], $replace);
            $res['msg'] = $res['msg'];
        }
        return new Response(self::$code, ['Content-Type' => 'application/json'], json_encode($res));
    }

    public static function success($msg = 'success', ?array $data = null, ?array $replace = []): Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg  = 'success';
        }

        return self::make(200, $msg, $data, $replace);
    }

    public static function fail($msg = 'fail', ?array $data = null, ?array $replace = []): Response
    {
        if (is_array($msg)) {
            $data = $msg;
            $msg  = 'fail';
        }

        return self::make(400, $msg, $data, $replace);
    }

    public static function status($status, $msg, $result = []): Response
    {
        $status = strtoupper($status);
        if (is_array($msg)) {
            $result = $msg;
            $msg    = 'success';
        }
        return self::success($msg, compact('status', 'result'));
    }
}
