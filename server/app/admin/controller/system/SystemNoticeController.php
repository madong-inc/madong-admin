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
use app\admin\validate\system\SystemNoticeValidate;
use app\common\services\system\SystemNoticeService;
use madong\services\email\MessagePushService;
use madong\utils\Json;
use support\Container;
use support\Request;

class SystemNoticeController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->validate=Container::make(SystemNoticeValidate::class);
        $this->service = Container::make(SystemNoticeService::class);
    }

    public function test(){
         MessagePushService::broadcastMessage();
//          MessagePushService::pushMessage(1, '发送私人信息', 2);
    }

}
