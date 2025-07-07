<?php
/**
 *+------------------
 * madong
 *+------------------
 * Copyright (c) https://gitee.com/motion-code  All rights reserved.
 *+------------------
 * Author: Mr. April (405784684@qq.com)
 *+------------------
 * Official Website: http://www.madong.cn
 */

namespace app\common\model\system;

use madong\admin\abstract\BaseModel;

/**
 * 全局菜单
 *
 * @property mixed $children
 * @author Mr.April
 * @since  1.0
 */
class SysMenu extends BaseModel
{

    protected $table = 'sys_menu';

    protected $primaryKey = 'id';

    protected $appends = ['created_date', 'updated_date'];

    protected $fillable = [
        'id',
        'pid',
        'app',
        'title',
        'code',
        'level',
        'type',
        'sort',
        'path',
        'component',
        'redirect',
        'icon',
        'is_show',
        'is_link',
        'link_url',
        'open_type',
        'is_cache',
        'is_sync',
        'is_affix',
        'variable',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'methods',
    ];

    /**
     * 菜单meta属性
     *
     * @param $data
     *
     * @return array
     */
    public static function getMetaAttribute($data): array
    {
        // 1.构建mate数组
        $title   = trans($data['title'], [], 'menu');
        $newData = [
            'icon'                     => $data['icon'] ?? '',
            'title'                    => $title,
            'menuVisibleWithForbidden' => true,
            'keepAlive' => true,
        ];

        // 2.添加fixed锁定菜单标记
        if (isset($data['is_affix']) && ($data['is_affix'] === 1 || $data['is_affix'] === '1')) {
            $newData['order']    = -1;
            $newData['affixTab'] = true;
        }

        // 3.是否隐藏菜单
        if (isset($data['is_show']) && $data['is_show'] == 0) {
            $newData['hideInMenu'] = true;
        }

        // 4.是否缓存
        if (isset($data['is_cache']) && (int)$data['is_cache'] == 1) {
            $newData['keepAlive'] = true;
        }

        //5.是否外链在新窗口打开
        if (isset($data['open_type']) && $data['open_type'] == 1) {
            $newData['link'] = true;
        }
        // 更多参数可以在这边添加
        return $newData;
    }

    /**
     * Id搜索
     */
    public function scopeId($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('id', $value);
        } else {
            $query->where('id', $value);
        }
    }

    /**
     * Type搜索
     */
    public function scopeType($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('type', $value);
        } else {
            $query->where('type', $value);
        }
    }

    public function scopeEnabled($query, $value)
    {
        if (is_array($value)) {
            $query->whereIn('enabled', $value);
        } else {
            $query->where('enabled', $value);
        }
    }

    /**
     * 自定义删除方法
     *
     * @return array|bool
     */
    public function deleteWithAllChildren(): array|bool
    {
        // 获取所有子级ID（递归）
        $allChildIds = $this->getAllChildrenIds();

        // 添加当前记录的ID
        $allChildIds[] = $this->id;

        // 批量删除所有相关记录
        $result = self::whereIn('id', $allChildIds)->delete();
        if ($result > 0) {
            return $allChildIds ?? [];
        }
        return false;
    }

    /**
     * 获取子级ids
     *
     * @return array
     */
    protected function getAllChildrenIds(): array
    {
        $childIds = $this->children()->pluck('id')->toArray(); // 获取直接子级的ID
        foreach ($this->children as $child) { // 遍历直接子级
            $childIds = array_merge($childIds, $child->getAllChildrenIds()); // 递归获取嵌套子级
        }
        return array_unique($childIds);
    }

    /**
     * 定义子级关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'pid');
    }

    /**
     * 默认链接
     */
    protected function initialize()
    {
        $this->connection = config('database.default');
    }
}
