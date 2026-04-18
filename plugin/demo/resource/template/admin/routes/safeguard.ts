import { AppRouteRecord } from '@/types/router'

export const safeguardRoutes: AppRouteRecord = {
  name: 'DemoSafeguard',
  path: '/demo-safeguard',
  meta: {
    title: 'menus.safeguard.title',
    icon: 'ri:shield-keyhole-line',
    module: 'demo'
  },
  children: [
    {
      path: 'server',
      name: 'DemoServer',
      component: '/safeguard/server',
      meta: {
        title: 'menus.safeguard.server',
        icon: 'ri:server-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}