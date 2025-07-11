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

namespace app\admin\validate\platform;

use app\common\model\platform\DbSetting;
use app\common\model\platform\Tenant;
use think\Validate;

class DbSettingValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'             => 'require|is_default',
        'name'           => 'require',
        'description'    => 'require',
        'driver'         => 'require',
        'database'       => 'require|unique_db',
        'host'           => 'require',
        'port'           => 'require',
        'username'       => 'require',
        'password'       => 'require'
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'name.require'                   => '数据中心名称必须填写',
        'driver.require'                 => 'DB类型不能为空',
        'database.require'               => '数据链接名不能为空',
        'database.unique_db'             => '数据连接名称已被占用',
        'host.require'                   => 'ip必须填写',
        'port.require'                   => '端口必须填写',
        'username.require'               => '数据库账号必须填写',
        'password.require'               => '数据库密码必须填写',
        'id.require'                     => '参数id不能为空',
        'id.is_default'                  => '系统内置不支持当前操作'
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
    protected function unique_id($value, $rule, array $data = []): bool
    {
        $query = DbSetting::where('name', $value);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 数据链接名重复验证
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function unique_db($value, $rule, array $data = []): bool
    {
        $query = DbSetting::where('database', $value);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 是否系统内置
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function is_default($value, $rule, array $data = []): bool
    {
        $query = DbSetting::where('is_default', 1);

        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', $data['id']);
        }
        return $query->count() === 0;
    }


    /**
     * 定义场景
     */
    protected $scene = [
        'store'   => [
            'name',
            'driver',
            'database',
            'host',
            'port',
            'username',
            'password',
        ],
        'update'  => [
            'id',
        ],
        'destroy' => [
            'id'
        ],
    ];
}
