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

namespace plugin\cmdr\app\controller;

use madong\admin\utils\Util;
use plugin\cmdr\app\common\Json;
use plugin\cmdr\app\service\Terminal;
use support\Request;
use support\Response;

/**
 * 脚本命令执行入口
 *
 * @author Mr.April
 * @since  1.0
 */
class Index
{

    /**
     * Web终端命令执行
     *
     * @param \support\Request $request
     *
     * @throws \Throwable
     */
    public function index(Request $request): void
    {
        $connection = $request->connection;
        $connection->send(new Response(200, [
            'Content-Type'                     => 'text/event-stream',
            'Cache-Control'                    => 'no-cache',
            'Connection'                       => 'keep-alive',
            'Access-Control-Allow-Origin'      => '*', // 或者允许所有来源(不推荐生产环境使用)
            'Access-Control-Allow-Credentials' => 'true', // 如果需要凭证
            'Access-Control-Expose-Headers'    => 'Content-Type', // 暴露必要的头
        ], "\r\n"));

        $generator = (new Terminal())->exec(true);
        foreach ($generator as $chunk) {
            $connection->send($chunk);
        }
    }

    /**
     * 更新终端配置
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function updateConfig(Request $request): Response
    {
        try {
            $data     = $request->all();
            $result   = Terminal::changeConfig($data);
            if (empty($result)) {
                throw new \Exception('更新异常');
            }
            Util::reloadWebman();
            return Json::success('操作成功');
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

}
