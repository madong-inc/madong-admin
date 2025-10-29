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

namespace app\admin\controller\monitor;

use support\Log;
use support\Request;
use support\Response;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\ServerSentEvents;
use Workerman\Timer;

class StreamController
{
    public function index(Request $request): Response
    {
        $connection = $request->connection;
        $connection->send(new Response(200, [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Headers' => '*',
            'Content-Type'                 => 'text/event-stream',
        ], "\r\n"));

        $id = Timer::add(2, function () use ($connection, &$id) {
            // 连接关闭时，清除定时器
            if ($connection->getStatus() !== TcpConnection::STATUS_ESTABLISHED) {
                Timer::del($id);
            }
            $connection->send(new ServerSentEvents(['event' => ':message', 'data' => 'hello', 'id' => 1]));
        });

        return response('', 200, [
            'Content-Type'  => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection'    => 'keep-alive',
        ]);
    }

}