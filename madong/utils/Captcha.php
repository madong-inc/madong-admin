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
        $img      = base64_encode($img_content);
        return compact('uuid', 'img');
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