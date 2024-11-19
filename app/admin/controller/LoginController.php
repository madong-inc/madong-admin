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

namespace app\admin\controller;

use support\Container;
use support\Request;
use madong\exception\AdminException;
use madong\utils\Captcha;
use madong\utils\Json;
use app\services\system\SystemUserService;

/**
 * 用户登录
 *
 * @author Mr.April
 * @since  1.0
 */
class LoginController extends Crud
{

    public function __construct()
    {
        parent::__construct();//调用父类构造函数
        $this->service = Container::make(SystemUserService::class);
    }

    /**
     * 重新初始化
     *
     * @return void
     */
    protected function initialize(): void
    {

    }

    /**
     * 是否开启验证码
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function getCaptchaOpenFlag(Request $request): \support\Response
    {
        return Json::success('ok', ['flag' => config('madong.captcha_open_flag')]);
    }

    /**
     * 获取验证码
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function captcha(Request $request): \support\Response
    {
        try {
            $captcha = new Captcha();
            $type    = $request->input('type', 'login');
            $result  = $captcha->captcha($request, $type);
            return Json::success('ok', $result);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 手机验证码
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function sendSms(Request $request): \support\Response
    {
        // 1.0生成验证码
        $captcha = new Captcha();
        $result  = $captcha->generateSmsCaptcha($request);

        // 2.0发送验证码到手机

        // 3.0 存储验证码到 Redis，设置过期时间（如 5 分钟）

        return Json::success('验证码发送成功', $result);
    }

    /**
     * 登陆
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function login(Request $request): \support\Response
    {

        try {
            $username  = $request->input('user_name');
            $password  = $request->input('password','');
            $phone     = $request->input('mobile_phone', '');
            $code      = $request->input('code', '');
            $uuid      = $request->input('uuid', '');
            $type      = $request->input('type', 'admin');
            $grantType = $request->input('grant_type', 'default');//refresh_token   sms   default 可以自行定义拓展登录方式

            $service = Container::make(SystemUserService::class);
            $captcha = new Captcha();
            if (config('captcha.captcha_open_flag') && $grantType === 'default') {
                if (!$captcha->check($uuid, $code)) {
                    throw new AdminException('图片验证码错误！');
                }
            }

            if ($grantType == 'sms' && !empty($phone)) {
                if (!$captcha->check($phone, $code)) {
                    throw new AdminException('手机验证码错误！');
                }
                $info = $service->get(['mobile_phone' => $phone]);
                if (empty($info)) {
                    throw new AdminException('当前手机未绑定用户！');
                }
                $username = $info->getData('user_name');
            }

            $data = $service->login($username, $password, $type, $grantType);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 注销
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function logout(Request $request): \support\Response
    {
        return Json::success('ok', []);
    }

    public function test(Request $request)
    {
        var_dump(123);

    }

}
