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


const generate: RouteRecordStringComponent[] = [
  {
    component: 'BasicLayout',
    meta: {
      hideChildrenInMenu: true,
      hideInMenu: true,
      title: $t('生成配置'),
    },
    name: 'dev:generate',
    path: '/dev/generate',
    children: [
      {
        meta: {
          icon: 'mingcute:profile-line',
          title: $t('生成配置'),
        },
        name: 'dev:generate:builder',
        path: '/dev/generate/builder',
        component: '/dev/builder/index',
      },
    ],
  },
];


const launch: RouteRecordStringComponent[] = [
  {
    component: 'BasicLayout',
    meta: {
      icon: 'ant-design:user-outlined',
      keepAlive: true,
      order: 1000,
      title: $t('发起流程'),
      hideInMenu: true,
      component: 'BasicLayout',
    },
    name: 'wf:manager',
    path: '/wf/launch/start',
    children: [
      {
        meta: {
          icon: 'ant-design:user-switch-outlined',
          title: $t('发起流程'),
          hideInMenu: true,
        },
        name: 'launch:start',
        path: '/wf/define/start',
        component: '/wf/define/start',
      },
    ],
  },
];

/**
 * 后端模式-前端静态路由
 */
export const localRoutesList: RouteRecordStringComponent[] = [
  // {
  //   component: 'BasicLayout',
  //   meta: {
  //     hideChildrenInMenu: true,
  //     icon: 'lucide:copyright',
  //     order: 9999,
  //     title: $t('demos.vben.about'),
  //   },
  //   name: 'About',
  //   path: '/about',
  //   children: [
  //     {
  //       component: '/_core/about/index',
  //       meta: {
  //         title: $t('demos.vben.about'),
  //       },
  //       name: 'VbenAbout',
  //       path: '/vben-admin/about',
  //     },
  //   ],
  // },
  ...personal,
  // ...generate,
  // ...launch
];
