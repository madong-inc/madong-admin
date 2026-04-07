<?php
declare(strict_types=1);

namespace app\api\listener;

use app\api\event\MemberInfoFetchedEvent;
use app\model\member\MemberTag;
use support\Container;

/**
 * 会员标签权限监听器
 *
 * 监听用户信息获取事件，自动添加标签权限
 */
class MemberTagPermissionListener
{
    /**
     * 处理事件
     */
    public function handle(MemberInfoFetchedEvent $event): void
    {
        try {
            $memberId = $event->memberId;

            // 查询会员的所有启用标签及权限
            $tags = MemberTag::with(['permissions'])
                ->whereHas('members', function ($query) use ($memberId) {
                    $query->where('member_id', $memberId);
                })
                ->where('enabled', 1)
                ->get();

            if ($tags->isEmpty()) {
                return;
            }

            // 收集所有权限
            $permissions = [];
            foreach ($tags as $tag) {
                foreach ($tag->permissions as $permission) {
                    // permission 是 Menu 对象，使用 code 字段
                    $permissions[] = $permission->code;
                }
            }

            // 添加到事件
            if (!empty($permissions)) {
                $event->addPermissions(array_unique($permissions));
            }

            // 也可以将标签信息添加到额外数据中（tags 字段已存在，这里可以不添加）

        } catch (\Exception $e) {
            // 记录日志但不影响主流程
            error_log('MemberTagPermissionListener error: ' . $e->getMessage());
        }
    }
}
