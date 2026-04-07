<?php
declare(strict_types=1);

namespace app\adminapi\validate\member;

use app\model\member\MemberLevel;
use core\base\BaseValidate;
use Illuminate\Validation\Rule;

/**
 * 会员等级验证器
 */
class MemberLevelValidate extends BaseValidate
{
    /**
     * 验证规则
     */
    public function rules(): array
    {
        $id = request()->route->param('id');

        return [
            'name'        => [
                'required',
                'max:50',
                Rule::unique(MemberLevel::class, 'name')->ignore($id, 'id'),
            ],
            'level'       => [
                'required',
                'integer',
                'min:1',
                Rule::unique(MemberLevel::class, 'level')->ignore($id, 'id'),
            ],
            'min_points'  => 'integer|min:0',
            'max_points'  => 'integer|min:0',
            'discount'    => 'numeric|between:0,1',
            'color'       => 'max:20',
            'description' => 'max:255',
            'enabled'      => 'in:0,1',
        ];
    }

    /**
     * 验证消息
     */
    protected array $message = [
        'name.required'      => '等级名称不能为空',
        'name.max'           => '等级名称不能超过50个字符',
        'name.unique'        => '等级名称已存在',
        'level.required'     => '等级值不能为空',
        'level.integer'      => '等级值必须为整数',
        'level.min'          => '等级值不能小于1',
        'level.unique'       => '等级值已存在',
        'min_points.integer' => '最低积分必须为整数',
        'min_points.min'     => '最低积分不能小于0',
        'max_points.integer' => '最高积分必须为整数',
        'max_points.min'     => '最高积分不能小于0',
        'discount.number'    => '折扣率必须为数字',
        'discount.between'   => '折扣率必须在0到1之间',
        'color.max'          => '等级颜色不能超过20个字符',
        'description.max'    => '等级描述不能超过255个字符',
        'enabled.in'          => '状态值不正确',
    ];

    /**
     * 验证场景
     */
    protected array $scene = [
        'store'  => [
            'name',
            'level',
            'min_points',
            'max_points',
            'discount',
            'color',
            'description',
            'enabled',
        ],
        'update' => [
            'name',
            'level',
            'min_points',
            'max_points',
            'discount',
            'color',
            'description',
            'enabled'
        ],
    ];

}