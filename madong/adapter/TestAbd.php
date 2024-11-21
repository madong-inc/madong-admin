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

namespace madong\adapter;


class TestAbd extends BaseDao
{

    public function setModel(): string
    {
        return TestModel::class;
    }



    public function tex(){
        return $this->selectList(['id'=>1]);

    }

}
