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

namespace app\dao\system;

use app\model\system\SystemDict;
use madong\basic\BaseDao;

class SystemDictDao extends BaseDao
{

    protected function setModel(): string
    {
        return SystemDict::class;
    }

    /**
     * 通过code获取字典项
     *
     * @param string $code
     *
     * @return array
     * @throws \Exception
     */
    public function findItemsByCode(string $code): array
    {
        $map1   = [
            'code'    => $code,
            'enabled' => 1,
        ];
        $result = parent::get($map1, ['*'], ['items']);
        if (empty($result)) {
            return [];
        }
        $items = $result->getAttribute('items');
        if (empty($items)) {
            return [];
        }
        $dataType = $result->getAttribute('data_type');
        foreach ($items as $item) {
            $value = $item->getAttribute('value');
            if ($dataType == 1) {
                $item->value = (string)$value; // 转换为字符串
            } elseif ($dataType == 2) {
                $item->value = (int)$value; // 转换为整型
            }
        }

        return $items->makeHidden(['id'])->map(function ($item) {
            return [
                'label' => $item->label,
                'value' => $item->value,
                'ext'   => $item->ext,
            ];
        })->toArray();
    }
}
