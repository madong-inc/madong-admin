<?php
declare(strict_types=1);
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

namespace app\adminapi\validate\gateway;

use app\model\gateway\RateLimiter;
use core\base\BaseValidate;

class RateLimiterValidate extends BaseValidate
{
    /**
     * 定义验证规则
     */
    protected array $rule = [
        'name'        => 'require|unique_name',
        'match_type'  => 'require',
        'methods'     => 'require',
        'path'        => 'require',
        'limit_value' => 'require',
        'period'      => 'require',
        'message'     => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected array $message = [
        'name.require'          => '规则名称不能为空',
        'name.unique_name'      => '规则名称已被占用',
        'match_type.require'    => '匹配类型不能为空',
        'http_methods.require'  => '请求方法不能为空',
        'path.require'          => '请求路径不能为空',
        'limit_value.unique_db' => '限制值必须填写',
        'period.require'        => '统计周期不能为空',
        'message.require'       => '提示消息不能为空',
    ];

    /**
     * 数据中心代码重复验证
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function unique_name($value, $rule, array $data = []): bool
    {
        $query = RateLimiter::where('name', $value);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 定义场景
     */
    protected array $scene = [
        'store'   => [
            'name',
            'match_type',
            'methods',
            'path',
            'limit_value',
            'period',
            'message',
        ],
        'update'  => [
            'id',
        ],
        'destroy' => [
            'id',
        ],
    ];
}
