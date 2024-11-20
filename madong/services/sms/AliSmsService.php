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

namespace madong\services\sms;

use app\services\system\SystemConfigService;
use madong\exception\AdminException;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use support\Container;

/**
 * 短信发送-阿里云
 *
 * @author Mr.April
 * @since  1.0
 */
class AliSmsService
{
    protected ?EasySms $easySms;

    public function __construct()
    {
        $systemConfigService = Container::make(SystemConfigService::class);
        $config              = $systemConfigService->getConfigContentValue('sms_setting');
        $this->easySms = new EasySms([
            'default' => [
                'driver' => 'aliyun',
                'config' => [
                    'access_key' => $config['access_key'] ?? '',//你的默认阿里云Access Key
                    'secret_key' => $config['secret_key'] ?? '',//你的默认阿里云Secret Key
                    'sign_name'  => $config['sign_name'] ?? '',//你的默认短信签名
                ],
            ],
        ]);
    }

    /**
     * @param string|int $to       手机号码
     * @param string     $template 短信模板CODE
     * @param array      $data     ['变量1' => '值1','变量2' => '值2']
     *
     * @return array
     */
    public function send(string|int $to, string $template, array $data = []): array
    {
        try {
            return $this->easySms->send($to, [
                'template' => $template,
                'data'     => $data,
            ]);
        } catch (NoGatewayAvailableException|\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }
}
