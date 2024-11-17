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

namespace madong\utils;

use madong\exception\AdminException;
use support\Redis;
use Ramsey\Uuid\Uuid;
use support\Request;
use Webman\Captcha\CaptchaBuilder;
use Webman\Captcha\PhraseBuilder;

class Captcha
{

    /**
     * 生成验证码
     *
     * @param \support\Request $request
     * @param string           $type
     *
     * @return array
     */
    public function captcha(Request $request, string $type = 'login'): array
    {
        $builder = new PhraseBuilder(4, 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ');

        $captcha = new CaptchaBuilder(null, $builder);
        $captcha->setBackgroundColor(242, 243, 245);
        $captcha->build(120, 36);
        $uuid   = Uuid::uuid4();
        $key    = $uuid->toString();
        $mode   = config('ingenstream.captcha.mode', 'session');
        $expire = config('ingenstream.captcha.expire', 300);
        $code   = strtolower($captcha->getPhrase());
        if ($mode === 'redis') {
            try {
                Redis::set($key, $code, 'EX', $expire);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => '验证码生成失败，请检查Redis配置']);
            }
        } else {
            $request->session()->set($key, $code);
        }
        $img_content = $captcha->get();
        $base64      = base64_encode($img_content);
        $base64      = 'data:image/png;base64,' . $base64;
        return compact('uuid', 'base64');
    }

    /**
     * 生成手机验证码
     *
     * @param \support\Request $request
     * @param int              $length
     *
     * @return array
     */
    public function generateSmsCaptcha(Request $request, int $length = 6): array
    {
        try {
            $uid = $request->input('mobile_phone');
            if (!$uid || !preg_match('/^\+?[0-9]{10,15}$/', $uid)) {
                throw new AdminException('无效的手机号');
            }

            // 生成 6 位数字验证码
            $code   = str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT); // 生成 6 位数字，前面补零
            $mode   = config('ingenstream.captcha.mode', 'session');
            $expire = config('ingenstream.captcha.expire', 300);

            if ($mode === 'redis') {
                try {
                    Redis::set($uid, $code, 'EX', $expire);
                } catch (\Exception $e) {
                    throw new AdminException('验证码生成失败，请检查Redis配置');
                }
            } else {
                $request->session()->set($uid, $code);
            }

            return [
                'code' => $code,
                'uid'  => $uid,
            ];
        } catch (\Exception $e) {
            throw new AdminException($e->getMessage());
        }
    }

    /**
     * 检查验证码
     */
    public function check(string $uuid, string|int $captcha): bool
    {
        $mode = config('ingenstream.captcha.mode', 'session');
        if ($mode === 'redis') {
            try {
                $code = Redis::get($uuid);
                Redis::del($uuid);
            } catch (\Exception $e) {
                return false;
            }
        } else {
            $code = session($uuid);
            session()->forget($uuid);
        }
        if (strtolower($captcha) !== $code) {
            return false;
        }
        return true;
    }
}