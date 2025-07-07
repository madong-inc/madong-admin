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

namespace app\admin\controller\system;

use app\admin\controller\Crud;
use app\admin\validate\system\SysNoticeValidate;
use app\common\services\system\SysNoticeService;
use madong\admin\services\email\MessagePushService;
use support\Container;

class SysNoticeController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->validate=Container::make(SysNoticeValidate::class);
        $this->service = Container::make(SysNoticeService::class);
    }

    public function test(){
         MessagePushService::broadcastMessage();
//          MessagePushService::pushMessage(1, '发送私人信息', 2);
    }

}
