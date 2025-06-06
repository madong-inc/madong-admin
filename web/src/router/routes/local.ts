import type { RouteRecordStringComponent } from '#/components/common/types';

import { $t } from '#/locale';

/**
 * 可以扫描staitic目录  将RouteRecordRaw转换RouteRecordStringComponent
 */
const personal: RouteRecordStringComponent[] = [
  {
    component: '/core/profile/index',
    meta: {
      icon: 'mingcute:profile-line',
      title: $t('system.user.profile.title'),
      hideInMenu: true,
      requireHomeRedirect: true,
    },
    name: 'Profile',
    path: '/profile',
  },

  {
    component: '/system/message/index',
    meta: {
      activePath: '/system/message',
      title: $t('system.message.list.title'),
      hideInMenu: true,
      requireHomeRedirect: true,
    },
    name: 'system_message',
    path: '/system/message',
  },
];



/**
 * 后端模式-前端静态路由
 */
export const localRoutesList: RouteRecordStringComponent[] = [
  // {
  //   component: 'BasicLayout',
  //   meta: {
  //     icon:'ant-design:home-outlined',
  //     order: -1,
  //     title: 'page.dashboard.title',
  //     // 不使用基础布局（仅在顶级生效）
  //     noBasicLayout: true,
  //   },
  //   name: 'Dashboard',
  //   path: '/',
  //   redirect: '/analytics',
  //   children: [
  //     {
  //       name: 'Analytics',
  //       path: '/analytics',
  //       component: '/dashboard/analytics/index',
  //       meta: {
  //         affixTab: true,
  //         title: 'page.dashboard.analytics',
  //       },
  //     },
  //     {
  //       name: 'Workspace',
  //       path: '/workspace',
  //       component: '/dashboard/workspace/index',
  //       meta: {
  //         title: 'page.dashboard.workspace',
  //       },
  //     },
  //   ]
  // },


  ...personal,
  // ...generate,
  // ...launch
];
