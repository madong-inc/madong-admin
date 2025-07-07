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

use Webman\Route;

/*
 * 无需授权的接口
 */
Route::group('/platform', function () {
    Route::get('/account-sets', [\app\admin\controller\platform\TenantController::class, 'accountSets'])->name('平台管理.账套中心.账套列表');
});

/**
 * 平台管理
 */
Route::group('/platform', function () {
    /**
     * 多数据源设置
     */
    Route::group(function () {
        Route::get('/db', [\app\admin\controller\platform\DbSettingController::class, 'index'])->name('平台管理.多数据源.列表');
        Route::get('/db/{id}', [\app\admin\controller\platform\DbSettingController::class, 'show'])->name('平台管理.多数据源.详情');
        Route::post('/db', [\app\admin\controller\platform\DbSettingController::class, 'store'])->name('平台管理.多数据源.保存');
        Route::put('/db', [\app\admin\controller\platform\DbSettingController::class, 'update'])->name('平台管理.多数据源.更新');
        Route::delete('/db/{id}', [\app\admin\controller\platform\DbSettingController::class, 'destroy'])->name('平台管理.多数据源.删除');
    });

    /**
     * 账套中心
     */
    Route::group(function () {
        Route::get('/tenant', [\app\admin\controller\platform\TenantController::class, 'index'])->name('平台管理.账套中心.列表');
        Route::get('/tenant-subscription-ids', [\app\admin\controller\platform\TenantController::class, 'getTenantSubscriptionIds'])->name('平台管理.账套中心.套餐ids');
        Route::get('/tenant/{id}', [\app\admin\controller\platform\TenantController::class, 'show'])->name('平台管理.账套中心.详情');
        Route::post('/tenant', [\app\admin\controller\platform\TenantController::class, 'store'])->name('平台管理.账套中心.保存');
        Route::post('/tenant/grant-subscription', [\app\admin\controller\platform\TenantController::class, 'grantSubscription'])->name('平台管理.租户套餐.关联租户');
        Route::put('/tenant', [\app\admin\controller\platform\TenantController::class, 'update'])->name('平台管理.账套中心.更新');
        Route::delete('/tenant/{id}', [\app\admin\controller\platform\TenantController::class, 'destroy'])->name('平台管理.账套中心.删除');
    });

    /**
     * 租户套餐
     */
    Route::group(function () {
        Route::get('/tenant-subscription', [\app\admin\controller\platform\TenantSubscriptionController::class, 'index'])->name('平台管理.租户套餐.列表');
        Route::get('/tenant-subscription-permission-ids', [\app\admin\controller\platform\TenantSubscriptionController::class, 'getPackagePermissionIds'])->name('平台管理.租户套餐.菜单id列表');
        Route::get('/tenant-subscription-tenant-ids', [\app\admin\controller\platform\TenantSubscriptionController::class, 'getPackageTenantIds'])->name('平台管理.租户套餐.菜单id列表');
        Route::get('/tenant-subscription/{id}', [\app\admin\controller\platform\TenantSubscriptionController::class, 'show'])->name('平台管理.租户套餐.详情');
        Route::post('/tenant-subscription', [\app\admin\controller\platform\TenantSubscriptionController::class, 'store'])->name('平台管理.租户套餐.保存');
        Route::post('/tenant-subscription/grant-permission', [\app\admin\controller\platform\TenantSubscriptionController::class, 'grantPermission'])->name('平台管理.租户套餐.授权权限');
        Route::post('/tenant-subscription/grant-tenant', [\app\admin\controller\platform\TenantSubscriptionController::class, 'grantTenant'])->name('平台管理.租户套餐.关联租户');
        Route::put('/tenant-subscription', [\app\admin\controller\platform\TenantSubscriptionController::class, 'update'])->name('平台管理.租户套餐.更新');
        Route::delete('/tenant-subscription/{id}', [\app\admin\controller\platform\TenantSubscriptionController::class, 'destroy'])->name('平台管理.租户套餐.删除');
    });

    /**
     * 成员管理
     */
    Route::group(function () {
        Route::get('/tenant-member', [\app\admin\controller\platform\TenantMemberController::class, 'index'])->name('平台管理.成员管理.列表');
        Route::get('/tenant-member/{id}', [\app\admin\controller\platform\TenantMemberController::class, 'show'])->name('平台管理.成员管理.详情');
        Route::post('/tenant-member', [\app\admin\controller\platform\TenantMemberController::class, 'store'])->name('平台管理.成员管理.保存');
        Route::put('/tenant-member', [\app\admin\controller\platform\TenantMemberController::class, 'update'])->name('平台管理.成员管理.更新');

        Route::delete('/tenant-member/{id}', [\app\admin\controller\system\SysAdminController::class, 'destroy'])->name('平台管理.成员管理.删除');
        Route::put('/tenant-member/reset-password', [\app\admin\controller\system\SysAdminController::class, 'changePassword'])->name('系统设置.用户管理.重置密码');
        Route::put('/tenant-member/locked', [\app\admin\controller\system\SysAdminController::class, 'locked'])->name('系统设置.用户管理.锁定用户');
        Route::put('/tenant-member/un-locked', [\app\admin\controller\system\SysAdminController::class, 'unLocked'])->name('系统设置.用户管理.取消锁定用户');
    });

    /**
     * 租户成员管理
     */
    Route::group(function () {
        Route::get('/tenant-admin', [\app\admin\controller\system\SysAdminTenantController::class, 'index'])->name('平台管理.授权租户.列表');
        Route::get('/tenant-admin/depts', [\app\admin\controller\system\SysAdminTenantController::class, 'getDept'])->name('平台管理.授权租户.租户部门列表');
        Route::get('/tenant-admin/tenants', [\app\admin\controller\system\SysAdminTenantController::class, 'getTenants'])->name('平台管理.授权租户.租户列表');
        Route::get('/tenant-admin/posts', [\app\admin\controller\system\SysAdminTenantController::class, 'getPost'])->name('平台管理.授权租户.租户职位列表');
        Route::get('/tenant-admin/roles', [\app\admin\controller\system\SysAdminTenantController::class, 'getRole'])->name('平台管理.授权租户.租户职位列表');
        Route::get('/tenant-admin/{id}', [\app\admin\controller\system\SysAdminTenantController::class, 'show'])->name('平台管理.授权租户.详情');
        Route::put('/tenant-admin', [\app\admin\controller\system\SysAdminTenantController::class, 'update'])->name('平台管理.授权租户.更新');
        Route::post('/tenant-admin', [\app\admin\controller\system\SysAdminTenantController::class, 'store'])->name('平台管理.授权租户.保存');
        Route::delete('/tenant-admin/{id}', [\app\admin\controller\system\SysAdminTenantController::class, 'destroy'])->name('平台管理.授权租户.删除');
    });

});

