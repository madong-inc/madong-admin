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

namespace app\admin\validate\system;

use app\common\model\system\SystemDataSource;
use think\Validate;

class SystemDataSourceValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'tenant_id'   => 'require|max:18|unique_id',
        'name'        => 'require',
        'db_domain'   => 'require|min:3|max:18|unique_db',
        'db_host'     => 'require',
        'db_port'     => 'require',
        'db_name'     => 'require',
        'db_password' => 'require',
        'phone'       => 'require|mobile',
        'id'          => 'require|is_system',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'tenant_id.require'   => '数据中心id必须填写',
        'tenant_id.max'       => '数据中心id最多不能超过18个字符',
        'tenant_id.unique_id' => '数据中心id已被占用',
        'name.require'        => '数据中心名称必须填写',
        'db_domain.require'   => '名称必须填写',
        'db_domain.min:3'     => '名称必须长度不能小于3',
        'db_domain.unique_db' => '数据连接名称已被占用',
        'db_host.require'     => 'ip必须填写',
        'db_port.require'     => '端口必须填写',
        'db_name.require'     => '数据库名称必须填写',
        'db_user.require'     => '数据库账号必须填写',
        'db_password.require' => '数据库密码必须填写',
        'phone.require'       => '手机号码必须填写',
        'phone.mobile'        => '无效手机号码',
        'id.require'          => '参数id不能为空',
        'id.is_system'        => '系统内置不支持当前操作',
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
        $query = SystemDataSource::where('tenant_id', $value);
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
        $query = SystemDataSource::where('db_domain', $value);
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
    protected function is_system($value, $rule, array $data = []): bool
    {
        $query = SystemDataSource::where('is_system', 1);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '=', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 定义场景
     */
    protected $scene = [
        'store'   => [
            'tenant_id',
            'name',
            'db_domain',
            'db_host',
            'db_port',
            'db_name',
            'db_password',
            'phone',
        ],
        'destroy' => [
            'id',
        ],
    ];
}
