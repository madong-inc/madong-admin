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
 *
 * 用户登录
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
     * 登陆
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function login(Request $request): \support\Response
    {

        try {
            $username = $request->input('username');
            $password = $request->input('password');
            $code     = $request->input('code', '');
            $uuid     = $request->input('uuid', '');
            $type     = $request->input('type', 'admin');
            $captcha  = new Captcha();
//            if (!$captcha->check($uuid, $code)) {
//                throw new AdminException('图片验证码错误！');
//            }
            $service = Container::make(SystemUserService::class);
            $data    = $service->login($username, $password, $type);
            return Json::success('ok', $data);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

}
