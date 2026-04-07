<?php

use Illuminate\Contracts\Validation\Factory as ValidationFactory;

return [
    'enable' => true,
    /**
     * 扩展自定义验证规则
     */
    'extends' => function (ValidationFactory $validator): void {
        // 注册手机号验证规则
        $validator->extend('mobile', function ($attribute, $value, $parameters) {
            return preg_match('/^1[3-9]\d{9}$/', $value);
        }, '无效的手机号码');
        
        // 注册手机号验证规则（别名）
        $validator->extend('phone', function ($attribute, $value, $parameters) {
            return preg_match('/^1[3-9]\d{9}$/', $value);
        }, '无效的手机号码');
    }
];