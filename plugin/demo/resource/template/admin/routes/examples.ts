import { AppRouteRecord } from '@/types/router'

export const examplesRoutes: AppRouteRecord = {
  name: 'DemoExamples',
  path: '/demo-examples',
  meta: {
    title: 'menus.examples.title',
    icon: 'ri:sparkling-line',
    module: 'demo'
  },
  children: [
    {
      path: 'forms',
      name: 'DemoForms',
      component: '/examples/forms',
      meta: {
        title: 'menus.examples.forms.title',
        icon: 'ri:form-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'tables',
      name: 'DemoTables',
      component: '/examples/tables',
      meta: {
        title: 'menus.examples.tables.title',
        icon: 'ri:table-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'tabs',
      name: 'DemoTabs',
      component: '/examples/tabs',
      meta: {
        title: 'menus.examples.tabs.title',
        icon: 'ri:folder-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'socket-chat',
      name: 'DemoSocketChat',
      component: '/examples/socket-chat',
      meta: {
        title: 'menus.examples.socketChat.title',
        icon: 'ri:chat-3-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'permission',
      name: 'DemoPermission',
      meta: {
        title: 'menus.examples.permission.title',
        icon: 'ri:fingerprint-line',
        module: 'demo'
      },
      children: [
        {
          path: 'switch-role',
          name: 'DemoPermissionSwitchRole',
          component: '/examples/permission/switch-role',
          meta: {
            title: 'menus.examples.permission.switchRole',
            icon: 'ri:contacts-line',
            keepAlive: true,
            module: 'demo'
          }
        },
        {
          path: 'button-auth',
          name: 'DemoPermissionButtonAuth',
          component: '/examples/permission/button-auth',
          meta: {
            title: 'menus.examples.permission.buttonAuth',
            icon: 'ri:mouse-line',
            keepAlive: true,
            module: 'demo',
            authList: [
              { title: '新增', authMark: 'add' },
              { title: '编辑', authMark: 'edit' },
              { title: '删除', authMark: 'delete' },
              { title: '导出', authMark: 'export' },
              { title: '查看', authMark: 'view' },
              { title: '发布', authMark: 'publish' },
              { title: '配置', authMark: 'config' },
              { title: '管理', authMark: 'manage' }
            ]
          }
        }
      ]
    }
  ]
}