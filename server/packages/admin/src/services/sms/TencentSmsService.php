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

namespace madong\admin\services\sms;

use app\common\services\system\SysConfigService;
use madong\admin\ex\AdminException;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use support\Container;

/**
 * 短信发送-腾讯
 *
 * @author Mr.April
 * @since  1.0
 */
class TencentSmsService
{

    /**
     * config表的group_cod字段值
     */
    const SETTING_GROUP_CODE = 'sms_setting';
    protected ?EasySms $easySms;

    public function __construct()
    {
        $systemConfigService = Container::make(SysConfigService::class);
        $config              = $systemConfigService->getConfigContentValue(self::SETTING_GROUP_CODE);
        $this->easySms       = new EasySms([
            'default' => [
                'driver' => 'tencent',
                'config' => [
                    'app_id'    => $config['access_key'] ?? '',//你的腾讯云App ID
                    'app_key'   => $config['secret_key'] ?? '',//你的腾讯云App Key
                    'sign_name' => $config['sign_name'] ?? '',//你的短信签名
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
