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
function getCurrentUser(bool $returnFullInfo = false): mixed
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

/**
 * 获取时间 N分钟前
 *
 * @param     $time
 * @param int $type 1 N分钟前  2 N分钟后
 *
 * @return false|string
 */
function getDateText($time = NULL, int $type = 1): bool|string
{
    if (!is_numeric($time)) {
        $time = strtotime($time);
    }
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t    = time() - $time; //时间差 （秒）
    $y    = date('Y', $time) - date('Y', time());//是否跨年

    $ext = '前';
    switch ($t) {
        case $t <= 0:
            $text = '刚刚';
            break;
        case $t < 60:
            $text = $t . '秒' . $ext; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟' . $ext; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时' . $ext; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time); //昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = date('m月d日 H:i', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365 && $y == 0:
            $text = date('m月d日', $time); //一年内
            break;
        default:
            $text = date('Y年m月d日', $time); //一年以前
            break;
    }
    return $text;
}
