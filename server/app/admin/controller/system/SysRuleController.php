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
use app\common\services\system\SysRuleCateService;
use app\common\services\system\SysRuleService;
use madong\admin\utils\Json;
use support\Container;
use support\Request;

/**
 * 路由规则
 *
 * @author Mr.April
 * @since  1.0
 */
class SysRuleController extends Crud
{

    public function __construct()
    {
        parent::__construct();
        $this->service = Container::make(SysRuleService::class);
    }

    /**
     * 路由列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function cate(Request $request): \support\Response
    {
        try {
            $service = new SysRuleCateService();
            $result  = $service->selectList(['enabled' => 1], '*', 0, 9999, 'sort desc', []);
            return $this->formatTree($result);
        } catch (\Exception $e) {
            return Json::fail('ok', []);
        }
    }

    /**
     * 路由列表
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function list(Request $request): \support\Response
    {
        try {
            [$where, $format, $limit, $field, $order, $page] = $this->selectInput($request);
            $methods         = [
                'select'     => 'formatSelect',
                'tree'       => 'formatTree',
                'table_tree' => 'formatTableTree',
                'normal'     => 'formatNormal',
            ];
            $format_function = $methods[$format] ?? 'formatNormal';
            $total           = $this->service->getCount($where);
            $list            = $this->service->selectList($where, $field, $page, $limit, $order, [], false);
            return call_user_func([$this, $format_function], $list, $total);
        } catch (\Throwable $e) {
            return Json::fail($e->getMessage());
        }
    }

    /**
     * 路由同步
     *
     * @param \support\Request $request
     *
     * @return \support\Response
     */
    public function sync(Request $request): \support\Response
    {
        try {
            $this->service->sync();
            return Json::success('同步成功', []);
        } catch (\Exception $e) {
            return Json::fail($e->getMessage());
        }
    }

}
