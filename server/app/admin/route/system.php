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

use Webman\Route;

/*
 * 无需授权的接口
 */
Route::group(function () {
    Route::post('/system/login', [\app\admin\controller\LoginController::class, 'login'])->name('系统设置.权限管理.登录');
    Route::post('/system/logout', [\app\admin\controller\LoginController::class, 'logout'])->name('系统设置.权限管理.注销');
    Route::post('/system/send-sms', [\app\admin\controller\LoginController::class, 'sendSms'])->name('系统设置.权限管理.发送手机验证码');
    Route::get('/system/captcha', [\app\admin\controller\LoginController::class, 'captcha'])->name('系统设置.权限管理.验证码');
    Route::get('/system/get-captcha-open-flag', [\app\admin\controller\LoginController::class, 'getCaptchaOpenFlag'])->name('系统设置.权限管理.是否开启验证码');
    Route::get('/system/config/info', [\app\admin\controller\system\SystemConfigController::class, 'getConfigInfo'])->name('系统设置.系统参数.配置详情');
    Route::get('/system/account-sets', [\app\admin\controller\system\SystemTenantController::class, 'accountSets'])->name('系统设置.租户管理.账套列表');
});

Route::group('/system', function () {
    /**
     * 用户模块
     */
    Route::group(function () {
        Route::get('/user', [\app\admin\controller\system\SystemUserController::class, 'index'])->name('系统设置.用户管理.列表');
        Route::get('/user/{id}', [\app\admin\controller\system\SystemUserController::class, 'show'])->name('系统设置.用户管理.详情');
        Route::put('/user', [\app\admin\controller\system\SystemUserController::class, 'update'])->name('系统设置.用户管理.更新');
        Route::put('/user/recovery', [\app\admin\controller\system\SystemUserController::class, 'recovery'])->name('系统设置.用户管理.恢复');
        Route::post('/user', [\app\admin\controller\system\SystemUserController::class, 'store'])->name('系统设置.用户管理.保存');
        Route::get('/user-select', [\app\admin\controller\system\SystemUserController::class, 'select'])->name('系统设置.用户管理.下拉列表');
        Route::delete('/user/{id}', [\app\admin\controller\system\SystemUserController::class, 'destroy'])->name('系统设置.用户管理.删除');
        Route::put('/user/reset-password', [\app\admin\controller\system\SystemUserController::class, 'changePassword'])->name('系统设置.用户管理.重置密码');

        Route::put('/user/locked', [\app\admin\controller\system\SystemUserController::class, 'locked'])->name('系统设置.用户管理.锁定用户');
        Route::put('/user/un-locked', [\app\admin\controller\system\SystemUserController::class, 'unLocked'])->name('系统设置.用户管理.取消锁定用户');
        Route::post('/user/grant-role', [\app\admin\controller\system\SystemUserController::class, 'grantRole'])->name('系统设置.用户管理.授权角色');
    });

    /**
     * 个人中心
     */
    Route::group(function () {
        Route::get('/profile/online-device', [\app\admin\controller\system\SystemUserController::class, 'onlineDevice'])->name('系统设置.个人中心.在线设备');
        Route::put('/profile/update-avatar', [\app\admin\controller\system\SystemUserController::class, 'updateAvatar'])->name('系统设置.个人中心.修改头像');
        Route::put('/profile/update-info', [\app\admin\controller\system\SystemUserController::class, 'updateInfo'])->name('系统设置.个人中心.修改个人信息');
        Route::put('/profile/update-pwd', [\app\admin\controller\system\SystemUserController::class, 'updatePwd'])->name('系统设置.个人中心.修改个人密码');
        Route::put('/profile/reset-password', [\app\admin\controller\system\SystemUserController::class, 'changePassword'])->name('系统设置.个人中心.重置密码');
        Route::delete('/profile/kickout-by-token-value/{id}', [\app\admin\controller\system\SystemUserController::class, 'kickoutByTokenValue'])->name('系统设置.个人中心.强制下线');
    });

    /**
     * Auth
     */
    Route::group(function () {
        Route::get('/auth/user-info', [\app\admin\controller\system\SystemAuthController::class, 'getUserInfo'])->name('系统设置.权限管理.获取用户详情');
        Route::get('/auth/user-menus', [\app\admin\controller\system\SystemAuthController::class, 'getUserMenus'])->name('系统设置.权限管理.获取用户菜单');
        Route::get('/auth/perm-code', [\app\admin\controller\system\SystemAuthController::class, 'getUserCodes'])->name('系统设置.权限管理.权限码');
        Route::get('/auth/role-menu-ids', [\app\admin\controller\system\SystemAuthController::class, 'roleMenuIds'])->name('系统设置.权限管理.根据角色ID获取菜单ID集合');
        Route::get('/auth/role-scope-ids', [\app\admin\controller\system\SystemAuthController::class, 'roleScopeIds'])->name('系统设置.权限管理.根据角色ID获取自定义数据范围ID集合');
        Route::get('/auth/role-menu-list', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('系统设置.权限管理.获取角色菜单列表');
        Route::get('/auth/role-menu-tree', [\app\admin\controller\system\SystemAuthController::class, 'dev'])->name('系统设置.权限管理.获取角色菜单树');
        Route::get('/auth/user-list-by-role-id', [\app\admin\controller\system\SystemAuthController::class, 'getUsersByRoleId'])->name('系统设置.权限管理.通过角色ID获取用户列表');
        Route::get('/auth/user-list-exclude-role-id', [\app\admin\controller\system\SystemAuthController::class, 'getUsersExcludingRole'])->name('系统设置.权限管理.获取用户列表-排除指定角色');
        Route::post('/auth/save-role-menu', [\app\admin\controller\system\SystemAuthController::class, 'saveRoleMenuRelation'])->name('系统设置.权限管理.保存角色菜单关系');
        Route::post('/auth/save-user-role', [\app\admin\controller\system\SystemAuthController::class, 'saveUserRoles'])->name('系统设置.权限管理.添加用户角色关系');
        Route::post('/auth/remove-user-role', [\app\admin\controller\system\SystemAuthController::class, 'removeUserRole'])->name('系统设置.权限管理.删除用户角色关系');
    });

    /**
     * 字典
     */
    Route::group(function () {
        Route::get('/dict', [\app\admin\controller\system\SystemDictController::class, 'index'])->name('系统设置.字典管理.列表');
        Route::get('/dict/enum-dict-list', [\app\admin\controller\system\SystemDictController::class, 'enumDictList'])->name('系统设置.字典管理.枚举字典');
        Route::get('/dict/get-by-dict-type', [\app\admin\controller\system\SystemDictController::class, 'getByDictType'])->name('系统设置.字典管理.根据字典编码获取字典');
        Route::get('/dict/{id}', [\app\admin\controller\system\SystemDictController::class, 'show'])->name('系统设置.字典管理.详情');
        Route::put('/dict', [\app\admin\controller\system\SystemDictController::class, 'update'])->name('系统设置.字典管理.更新');
        Route::put('/dict/change-status', [\app\admin\controller\system\SystemDictController::class, 'changeStatus'])->name('系统设置.字典管理.更新状态');
        Route::post('/dict', [\app\admin\controller\system\SystemDictController::class, 'store'])->name('系统设置.字典管理.保存');
        Route::delete('/dict/{id}', [\app\admin\controller\system\SystemDictController::class, 'destroy'])->name('系统设置.字典管理.删除');
    });

    /**
     * 字典数据
     */
    Route::group(function () {
        Route::get('/dict-item', [\app\admin\controller\system\SystemDictItemController::class, 'index'])->name('系统设置.字典数据.列表');
        Route::get('/dict-item/{id}', [\app\admin\controller\system\SystemDictItemController::class, 'show'])->name('系统设置.字典数据.详情');
        Route::put('/dict-item', [\app\admin\controller\system\SystemDictItemController::class, 'update'])->name('系统设置.字典数据.更新');
        Route::post('/dict-item', [\app\admin\controller\system\SystemDictItemController::class, 'store'])->name('系统设置.字典数据.保存');
        Route::delete('/dict-item/{id}', [\app\admin\controller\system\SystemDictItemController::class, 'destroy'])->name('系统设置.字典数据.删除');
    });

    /**
     * 菜单
     */
    Route::group(function () {
        Route::get('/menu', [\app\admin\controller\system\SystemMenuController::class, 'index'])->name('系统设置.菜单管理.列表');
        Route::get('/menu/{id}', [\app\admin\controller\system\SystemMenuController::class, 'show'])->name('系统设置.菜单管理.详情');
        Route::put('/menu', [\app\admin\controller\system\SystemMenuController::class, 'update'])->name('系统设置.菜单管理.更新');
        Route::post('/menu', [\app\admin\controller\system\SystemMenuController::class, 'store'])->name('系统设置.菜单管理.保存');
        Route::post('/menu/batch-store', [\app\admin\controller\system\SystemMenuController::class, 'batchStore'])->name('系统设置.菜单管理.批量保存');
        Route::delete('/menu/{id}', [\app\admin\controller\system\SystemMenuController::class, 'destroy'])->name('系统设置.菜单管理.删除');
        Route::post('/menu/app-list', [\app\admin\controller\system\SystemMenuController::class, 'dev'])->name('系统设置.菜单管理.应用列表');
    });

    /**
     * 角色
     */
    Route::group(function () {
        Route::get('/role', [\app\admin\controller\system\SystemRoleController::class, 'index'])->name('系统设置.角色管理.列表');
        Route::get('/role/{id}', [\app\admin\controller\system\SystemRoleController::class, 'show'])->name('系统设置.角色管理.详情');
        Route::put('/role', [\app\admin\controller\system\SystemRoleController::class, 'update'])->name('系统设置.角色管理.更新');
        Route::put('/role/data-scope', [\app\admin\controller\system\SystemRoleController::class, 'dataScope'])->name('系统设置.角色管理.数据权限');
        Route::put('/role/change-status', [\app\admin\controller\system\SystemRoleController::class, 'changeStatus'])->name('系统设置.角色管理.更新状态');
        Route::post('/role', [\app\admin\controller\system\SystemRoleController::class, 'store'])->name('系统设置.角色管理.保存');
        Route::delete('/role/{id}', [\app\admin\controller\system\SystemRoleController::class, 'destroy'])->name('系统设置.角色管理.删除');
    });

    /**
     * 部门
     */
    Route::group(function () {
        Route::get('/dept', [\app\admin\controller\system\SystemDeptController::class, 'index'])->name('系统设置.部门管理.列表');
        Route::get('/dept/{id}', [\app\admin\controller\system\SystemDeptController::class, 'show'])->name('系统设置.部门管理.详情');
        Route::put('/dept', [\app\admin\controller\system\SystemDeptController::class, 'update'])->name('系统设置.部门管理.更新');
        Route::post('/dept', [\app\admin\controller\system\SystemDeptController::class, 'store'])->name('系统设置.部门管理.保存');
        Route::delete('/dept/{id}', [\app\admin\controller\system\SystemDeptController::class, 'destroy'])->name('系统设置.部门管理.删除');
    });

    /**
     * 职位
     */
    Route::group(function () {
        Route::get('/post', [\app\admin\controller\system\SystemPostController::class, 'index'])->name('系统设置.职位管理.列表');
        Route::get('/post/{id}', [\app\admin\controller\system\SystemPostController::class, 'show'])->name('系统设置.职位管理.详情');
        Route::put('/post', [\app\admin\controller\system\SystemPostController::class, 'update'])->name('系统设置.职位管理.更新');
        Route::put('/post/change-status', [\app\admin\controller\system\SystemPostController::class, 'changeStatus'])->name('系统设置.职位管理.更新状态');
        Route::post('/post', [\app\admin\controller\system\SystemPostController::class, 'store'])->name('系统设置.职位管理.保存');
        Route::delete('/post/{id}', [\app\admin\controller\system\SystemPostController::class, 'destroy'])->name('系统设置.职位管理.删除');
    });

    /**
     * 日志
     */
    Route::group('/logs', function () {
        /**
         * 登录日志
         */
        Route::get('/login', [\app\admin\controller\system\SystemLoginLogController::class, 'index'])->name('系统设置.登录日志.列表');
        Route::get('/login/{id}', [\app\admin\controller\system\SystemLoginLogController::class, 'show'])->name('系统设置.登录日志.详情');
        Route::delete('/login/{id}', [\app\admin\controller\system\SystemLoginLogController::class, 'destroy'])->name('系统设置.登录日志.删除');

        /**
         * 操作日志
         */
        Route::get('/operate', [\app\admin\controller\system\SystemOperateLogController::class, 'index'])->name('系统设置.操作日志.列表');
        Route::get('/operate/{id}', [\app\admin\controller\system\SystemOperateLogController::class, 'show'])->name('系统设置.操作日志.详情');
        Route::delete('/operate/{id}', [\app\admin\controller\system\SystemOperateLogController::class, 'destroy'])->name('系统设置.操作日志.删除');
        Route::post('/operate/export', [\app\admin\controller\system\SystemOperateLogController::class, 'export'])->name('系统设置.操作日志.导出');
    });

    /**
     * 附件管理
     */
    Route::group(function () {
        Route::get('/files', [\app\admin\controller\system\SystemUploadController::class, 'index'])->name('系统设置.附件管理.附件列表');
        Route::get('/files/download-by-id/{id}', [\app\admin\controller\system\SystemUploadController::class, 'downloadResourceById'])->name('系统设置.附件管理.通过id下载文件');
        Route::get('/files/download-by-hash/{hash}', [\app\admin\controller\system\SystemUploadController::class, 'downloadResourceByHash'])->name('系统设置.附件管理.通过hash下载文件');
        Route::get('/files/{id}', [\app\admin\controller\system\SystemUploadController::class, 'show'])->name('系统设置.附件管理.附件详情');
        Route::post('/files/fetch-and-save-image', [\app\admin\controller\system\SystemUploadController::class, 'downloadNetworkImage'])->name('系统设置.附件管理.上传网络图片');
        Route::post('/files/upload-image', [\app\admin\controller\system\SystemUploadController::class, 'uploadImage'])->name('系统设置.附件管理.上传图片');
        Route::post('/files/upload-file', [\app\admin\controller\system\SystemUploadController::class, 'uploadFile'])->name('系统设置.附件管理.上传文件');
        Route::delete('/files/{id}', [\app\admin\controller\system\SystemUploadController::class, 'destroy'])->name('系统设置.附件管理.删除文件');
    });

    /**
     * 配置管理
     */
    Route::group(function () {
        Route::post('/config', [\app\admin\controller\system\SystemConfigController::class, 'store'])->name('系统设置.系统参数.保存');
    });

    /**
     * 回收站管理
     */
    Route::group(function () {
        Route::get('/recycle-bin', [\app\admin\controller\system\SystemRecycleBinController::class, 'index'])->name('系统设置.回收站管理.列表');
        Route::get('/recycle-bin/{id}', [\app\admin\controller\system\SystemRecycleBinController::class, 'show'])->name('系统设置.回收站管理.详情');
        Route::put('/recycle-bin', [\app\admin\controller\system\SystemRecycleBinController::class, 'restore'])->name('系统设置.回收站管理.恢复数据');
        Route::delete('/recycle-bin/{id}', [\app\admin\controller\system\SystemRecycleBinController::class, 'destroy'])->name('系统设置.回收站管理.永久删除');
    });

    /**
     * 通知公告
     */
    Route::group(function () {
        Route::get('/notice', [\app\admin\controller\system\SystemNoticeController::class, 'index'])->name('系统设置.通知公告.列表');
        Route::get('/notice/{id}', [\app\admin\controller\system\SystemNoticeController::class, 'show'])->name('系统设置.通知公告.详情');
        Route::post('/notice', [\app\admin\controller\system\SystemNoticeController::class, 'store'])->name('系统设置.通知公告.保存');
        Route::put('/notice', [\app\admin\controller\system\SystemNoticeController::class, 'update'])->name('系统设置.通知公告.更新');
        Route::delete('/notice/{id}', [\app\admin\controller\system\SystemNoticeController::class, 'destroy'])->name('系统设置.通知公告.删除');
    });

    /**
     * 系统消息
     */
    Route::group(function () {
        Route::get('/message', [\app\admin\controller\system\SystemMessageController::class, 'index'])->name('系统设置.系统消息.列表');
        Route::get('/message/{id}', [\app\admin\controller\system\SystemMessageController::class, 'show'])->name('系统设置.系统消息.详情');
        Route::post('/message', [\app\admin\controller\system\SystemMessageController::class, 'store'])->name('系统设置.系统消息.保存');
        Route::put('/message', [\app\admin\controller\system\SystemMessageController::class, 'update'])->name('系统设置.系统消息.更新');
        Route::put('/message/update-read', [\app\admin\controller\system\SystemMessageController::class, 'updateRead'])->name('系统设置.系统消息.标记已读');
        Route::delete('/message/{id}', [\app\admin\controller\system\SystemMessageController::class, 'destroy'])->name('系统设置.系统消息.删除');
        Route::post('/message/notify-on-first-login-to-all', [\app\admin\controller\system\SystemMessageController::class, 'notifyOnFirstLoginToAll'])->name('系统设置.系统消息.投送公告&未读消息');
    });

    /**
     * 接口列表
     */
    Route::group(function () {
        Route::get('/rule-cate', [\app\admin\controller\system\SystemRuleController::class, 'cate'])->name('系统设置.接口.分类');
        Route::get('/rule-list', [\app\admin\controller\system\SystemRuleController::class, 'list'])->name('系统设置.接口.列表');
        Route::post('/rule-sync', [\app\admin\controller\system\SystemRuleController::class, 'sync'])->name('系统设置.接口.同步');
    });

    /**
     * 租户管理
     */
    Route::group(function () {
        Route::get('/tenant', [\app\admin\controller\system\SystemTenantController::class, 'index'])->name('系统设置.租户管理.列表');
        Route::get('/tenant-user', [\app\admin\controller\system\SystemTenantController::class, 'tenantUserList'])->name('系统设置.租户管理.用户列表');
        Route::get('/tenant/{id}', [\app\admin\controller\system\SystemTenantController::class, 'show'])->name('系统设置.租户管理.详情');
        Route::post('/tenant', [\app\admin\controller\system\SystemTenantController::class, 'store'])->name('系统设置.租户管理.保存');
        Route::put('/tenant', [\app\admin\controller\system\SystemTenantController::class, 'update'])->name('系统设置.租户管理.更新');
        Route::put('/tenant/change-status', [\app\admin\controller\system\SystemTenantController::class, 'changeStatus'])->name('系统设置.租户管理.更新状态');
        Route::delete('/tenant/{id}', [\app\admin\controller\system\SystemTenantController::class, 'destroy'])->name('系统设置.租户管理.删除');
    });

    /**
     * 租户套餐管理
     */
    Route::group(function () {
        Route::get('/tenant-package', [\app\admin\controller\system\SystemTenantPackageController::class, 'index'])->name('系统设置.租户套餐.列表');
        Route::get('/tenant-package-menu-ids', [\app\admin\controller\system\SystemTenantPackageController::class, 'packageMenuIds'])->name('系统设置.租户套餐.授权菜单id集合');
        Route::get('/tenant-package/{id}', [\app\admin\controller\system\SystemTenantPackageController::class, 'show'])->name('系统设置.租户套餐.详情');
        Route::post('/tenant-package', [\app\admin\controller\system\SystemTenantPackageController::class, 'store'])->name('系统设置.租户套餐.保存');
        Route::put('/tenant-package', [\app\admin\controller\system\SystemTenantPackageController::class, 'update'])->name('系统设置.租户套餐.更新');
        Route::delete('/tenant-package/{id}', [\app\admin\controller\system\SystemTenantPackageController::class, 'destroy'])->name('系统设置.租户套餐.删除');
    });

})->middleware([
    app\common\middleware\AllowCrossOriginMiddleware::class,
    app\admin\middleware\AdminAuthTokenMiddleware::class,
    app\admin\middleware\AdminAuthPermissionMiddleware::class,
    app\admin\middleware\AdminLogMiddleware::class,
]);

