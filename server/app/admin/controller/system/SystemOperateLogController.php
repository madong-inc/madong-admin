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
use app\common\model\system\SystemOperateLog;
use app\common\services\system\SystemConfigService;
use app\common\services\system\SystemOperateLogService;
use Illuminate\Support\Carbon;
use madong\services\excel\ExcelExportService;
use madong\utils\Json;
use support\Container;
use support\Request;

/**
 * 行为日志
 *
 * @author Mr.April
 * @since  1.0
 */
class SystemOperateLogController extends Crud
{
    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SystemOperateLogService::class);
    }

}
