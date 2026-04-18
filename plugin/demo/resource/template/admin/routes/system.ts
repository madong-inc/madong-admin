import { AppRouteRecord } from '@/types/router'

export const systemRoutes: AppRouteRecord = {
  name: 'DemoSystem',
  path: '/demo-system',
  meta: {
    title: 'menus.system.title',
    icon: 'ri:settings-3-line',
    roles: ['R_SUPER', 'R_ADMIN'],
    module: 'demo'
  },
  children: [
    {
      path: 'user',
      name: 'DemoUser',
      component: '/system/user',
      meta: {
        title: 'menus.system.user',
        icon: 'ri:user-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'role',
      name: 'DemoRole',
      component: '/system/role',
      meta: {
        title: 'menus.system.role',
        icon: 'ri:admin-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'menu',
      name: 'DemoMenu',
      component: '/system/menu',
      meta: {
        title: 'menus.system.menu',
        icon: 'ri:menu-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'nested',
      name: 'DemoNested',
      meta: {
        title: 'menus.system.nested',
        icon: 'ri:node-tree',
        module: 'demo'
      },
      children: [
        {
          path: 'menu1',
          name: 'DemoMenu1',
          component: '/system/nested/menu1',
          meta: {
            title: 'menus.system.nested.menu1',
            icon: 'ri:menu-line',
            keepAlive: false,
            module: 'demo'
          }
        },
        {
          path: 'menu2',
          name: 'DemoMenu2',
          component: '/system/nested/menu2',
          meta: {
            title: 'menus.system.nested.menu2',
            icon: 'ri:menu-line',
            keepAlive: false,
            module: 'demo'
          }
        },
        {
          path: 'menu3',
          name: 'DemoMenu3',
          meta: {
            title: 'menus.system.nested.menu3',
            icon: 'ri:menu-line',
            module: 'demo'
          },
          children: [
            {
              path: 'menu3-2',
              name: 'DemoMenu3-2',
              component: '/system/nested/menu3/menu3-2',
              meta: {
                title: 'menus.system.nested.menu3-2',
                icon: 'ri:menu-line',
                keepAlive: false,
                module: 'demo'
              }
            }
          ]
        }
      ]
    },
    {
      path: 'user-center',
      name: 'DemoUserCenter',
      component: '/system/user-center',
      meta: {
        title: 'menus.system.userCenter',
        icon: 'ri:user-settings-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}