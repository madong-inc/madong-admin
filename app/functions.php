<?php
/**
 * Here is your custom functions.
 */

/**
 * 返回当前系统登录用户
 *
 * @param bool $returnFullInfo
 *
 * @return null
 */
function getCurrentUser(bool $returnFullInfo = false):mixed
{
    $request = request();
    if (!$request->hasMacro('adminInfo')) {
        return null;
    }
    $adminInfo = $request->adminInfo();
    if (!$adminInfo) {
        return null;
    }
    // 根据参数决定返回值
    return $returnFullInfo ? $adminInfo : $adminInfo['id'];
}
