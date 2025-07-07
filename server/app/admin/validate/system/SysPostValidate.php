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

use app\common\model\system\SysPost;
use think\Validate;

/**
 * 用户角色验证器
 */
class SysPostValidate extends Validate
{
    /**
     * 定义验证规则
     */
    protected $rule = [
        'dept_id' => 'require',
        'code'    => 'require|alphaNum|unique',
        'name'    => 'require|max:16',
        'sort'    => 'number',
        'enabled' => 'require',
    ];

    /**
     * 定义错误信息
     */
    protected $message = [
        'code.require'  => '职位标识必须填写',
        'code.alphaNum' => '职位标识只能由英文字或者数字母组成',
        'code.unique'   => '职位代码已被占用',
        'name.require'  => '职位名称必须填写',
        'name.max'      => '职位名称最多不能超过16个字符',
        'enabled'       => '状态必须填写',
    ];

    /**
     * 重复验证
     *
     * @param        $value
     * @param        $rule
     * @param array  $data
     *
     * @return bool
     */
    protected function unique($value, $rule, array $data = []): bool
    {
        $query = SysPost::where('code', $value);
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
        'store'  => [
            'code',
            'name',
            'sort',
            'enabled',
        ],
        'update' => [
            'code',
            'name',
            'sort',
            'enabled',
        ],
    ];

}
