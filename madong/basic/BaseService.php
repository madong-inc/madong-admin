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

namespace madong\basic;


use madong\trait\ServicesTrait;
use think\facade\Db;

abstract class BaseService
{

    use ServicesTrait;

    /**
     * 模型注入
     *
     * @var object
     */
    protected object $dao;

    /**
     * 缓存管理
     *
     * @return \madong\basic\CacheService
     */
    public function cacheDriver(): CacheService
    {
        return new CacheService($this->dao->getTableName());
    }

    /**
     * 获取分页配置
     *
     * @param bool $isPage
     * @param bool $isRelieve
     *
     * @return int[]
     */
    public function getPageValue(bool $isPage = true, bool $isRelieve = true): array
    {
        // 获取请求实例
        $request = request();
        $page    = $limit = 0;
        if ($isPage) {
            $page  = $request->input(Config('thinkorm.page.pageKey', 'page') . '/d', 0);
            $limit = $request->input(Config('thinkorm.page.limitKey', 'limit') . '/d', 0);
        }
        $limitMax     = Config('thinkorm.page.limitMax');
        $defaultLimit = Config('thinkorm.page.defaultLimit', 10);
        if ($limit > $limitMax && $isRelieve) {
            $limit = $limitMax;
        }
        return [(int)$page, (int)$limit, (int)$defaultLimit, (int)$limitMax];
    }

    /**
     * 数据库事务操作
     *
     * @param callable $closure
     * @param bool     $isTran
     *
     * @return mixed
     */
    public function transaction(callable $closure, bool $isTran = true): mixed
    {
        return $isTran ? DB::transaction($closure) : $closure();
    }

    /**
     * 创建token
     *
     * @param int|string $id
     * @param string     $type
     * @param string     $pwd
     *
     * @return array
     * @throws \app\exception\AdminException
     */
    public function createToken(int|string $id, string $type, string $pwd = ''): array
    {
        /** @var JwtAuth $jwtAuth */
        $jwtAuth = Container::make(JwtAuth::class);
        return $jwtAuth->createToken($id, $type, ['pwd' => md5($pwd)]);
    }


    /**
     * 密码hash加密
     *
     * @param string $password
     *
     * @return false|string|null
     */
    public function passwordHash(string $password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->dao, $name], $arguments);
    }
}
