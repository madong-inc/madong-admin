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

use app\common\model\platform\Tenant;
use app\common\model\system\SysAdmin;
use core\enum\platform\IsolationMode;
use think\Validate;

class TenantValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'id'                => 'require',
        'db_name'           => 'unique_db',
        'code'              => 'require',
        'type'              => 'require',
        'contact_person'    => 'require',
        'contact_phone'     => 'require',
        'company_name'      => 'require|unique_name',
        'license_number'    => 'require',
        'address'           => 'require',
        'description'       => 'require',
        'domain'            => 'require',
        'enabled'           => 'require',
        'account'           => 'require|unique_admin',
        'gran_subscription' => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'id.require'                => '参数ID不能为空',
        'db_name.unique_db'         => '数据源已被使用',
        'code.require'              => '账套code不能为空',
        'company_name.unique_name'  => '账套名已被占用',
        'type.require'              => '账套类型不能为空',
        'contact_person.require'    => '企业联系人不能为空',
        'contact_phone.require'     => '企业联系手机号不能为空',
        'company_name.require'      => '企业名称不能为空',
        'license_number.require'    => '企业信用代码',
        'address.require'           => '企业地址不能为空',
        'description.require'       => '描述',
        'domain.require'            => '企业域名',
        'enabled.require'           => '状态不能为空',
        'account.require'           => '账号不能为空',
        'account.unique_admin'      => '账号已被使用',
        'gran_subscription.require' => '授权套餐不能为空',
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
        $query = Tenant::where('company_name', $value);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 管理员账号唯一验证
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function unique_admin($value, $rule, array $data = []): bool
    {
        $query = SysAdmin::where('user_name', $value);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 限制一个数据库只能一个租户
     *
     * @param       $value
     * @param       $rule
     * @param array $data
     *
     * @return bool
     */
    protected function unique_db($value, $rule, array $data = []): bool
    {
        if ($data['isolation_mode'] == IsolationMode::FIELD_ISOLATION->value) {
            //字段隔离模式跳过验证
            return true;
        }
        $query = Tenant::where('db_name', $value);
        // 如果是更新操作，可以排除当前记录
        if (isset($data['id'])) {
            $query->where('id', '<>', $data['id']);
        }
        return $query->count() === 0;
    }

    /**
     * 定义场景
     */
    protected $scene = [
        'store'              => [
            'db_name',
            'contact_person',
            'contact_phone',
            'company_name',
            'license_number',
            'address',
            'gran_subscription',
        ],
        'update'             => [
            'id',
            'company_name'
        ],
        'destroy'            => [
            'id',
        ],
        'grant_subscription' => [
            'id',
        ],
    ];
}
