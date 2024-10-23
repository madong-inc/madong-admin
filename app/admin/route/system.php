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

use support\Request;
use Webman\Route;

/*
 * 无需授权的接口
 */
Route::group(function () {
    Route::post('/system/login', [\app\admin\controller\LoginController::class, 'login'])->name('登录');
    Route::post('/system/logout', [\app\admin\controller\LoginController::class, 'logout'])->name('注销');
    Route::get('/auth/captcha', [\app\admin\controller\LoginController::class, 'captcha'])->name('验证码');
});

Route::group('/system', function () {
    /**
     * 用户模块
     */
    Route::group(function () {
        Route::get('/user', [\app\admin\controller\system\SystemUserController::class, 'index'])->name('列表');
        Route::get('/user/{id}', [\app\admin\controller\system\SystemUserController::class, 'show'])->name('详情');
        Route::put('/user/{id}', [\app\admin\controller\system\SystemUserController::class, 'update'])->name('更新');
        Route::put('/user/{id}/recovery', [\app\admin\controller\system\SystemUserController::class, 'recovery'])->name('恢复');
        Route::post('/user', [\app\admin\controller\system\SystemUserController::class, 'store'])->name('保存');
        Route::post('/user-select', [\app\admin\controller\system\SystemUserController::class, 'select'])->name('下拉列表');
        Route::delete('/user/{id}', [\app\admin\controller\system\SystemUserController::class, 'destroy'])->name('删除');

        Route::post('/user/update-avatar', [\app\admin\controller\system\SystemUserController::class, 'dev'])->name('修改头像');
        Route::post('/user/update-info', [\app\admin\controller\system\SystemUserController::class, 'dev'])->name('修改个人信息');
        Route::post('/user/update-pwd', [\app\admin\controller\system\SystemUserController::class, 'dev'])->name('修改个人密码');
        Route::post('/user/perm-code', [\app\admin\controller\system\SystemUserController::class, 'dev'])->name('权限标识');
        Route::post('/user/reset-password', [\app\admin\controller\system\SystemUserController::class, 'dev'])->name('重置密码');
    });

    /**
     * Auth
     */
    Route::group(function () {
        Route::get('/auth/user-info', [\app\admin\controller\system\SystemUserController::class, 'info'])->name('用户详情');
        Route::post('/auth/codes', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('权限码');
        Route::post('/auth/save-role-menu', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('保存角色菜单关系');
        Route::post('/auth/role-menu-ids', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('根据角色ID获取菜单ID集合');
        Route::post('/auth/role-menu-list', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('获取角色菜单列表');
        Route::post('/auth/role-menu-tree', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('获取角色菜单树');
        Route::post('/auth/user-list-by-role-id', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('通过角色ID获取用户列表');
        Route::post('/auth/user-list-exclude-role-id', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('获取用户列表-排除指定角色');
        Route::post('/auth/save-user-role', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('添加用户角色关系');
        Route::post('/auth/remove-user-role', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('删除用户角色关系');
    });

})->middleware([
    app\middleware\AllowCrossOriginMiddleware::class,
    app\admin\middleware\AdminAuthTokenMiddleware::class,
    app\admin\middleware\AdminAuthPermissionMiddleware::class,
    app\admin\middleware\AdminLogMiddleware::class,
]);

