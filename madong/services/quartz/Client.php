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

class Client
{

    /**
     * 像redis 发送请求
     * @param array $param
     * @return bool
     */
    public static function request(array $param)
    {
        $listen = 'tcp://' . config('plugin.wf.app.listen');
        try{
            $client = stream_socket_client($listen);
            fwrite($client, json_encode($param) . "\n"); // text协议末尾有个换行符"\n"
            $result = fgets($client);
            $arr = json_decode($result,true);
            if ($arr['code'] == 200){
                return true;
            }
            return false;
        }catch (\Throwable $e){
            throw new \Exception("请求任务server失败,请检查防火墙或者配置端口是否一致，{$listen}");
        }
    }


}
