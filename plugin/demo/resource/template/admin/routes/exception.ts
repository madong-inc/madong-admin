import { AppRouteRecord } from '@/types/router'

export const exceptionRoutes: AppRouteRecord = {
  name: 'DemoException',
  path: '/demo-exception',
  meta: {
    title: 'menus.exception.title',
    icon: 'ri:error-warning-line',
    module: 'demo'
  },
  children: [
    {
      path: '403',
      name: 'Demo403',
      component: '/exception/403',
      meta: {
        isFullPage: true,
        title: 'menus.exception.403',
        icon: 'ri:forbid-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: '404',
      name: 'Demo404',
      component: '/exception/404',
      meta: {
        isFullPage: true,
        title: 'menus.exception.404',
        icon: 'ri:file-search-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: '500',
      name: 'Demo500',
      component: '/exception/500',
      meta: {
        isFullPage: true,
        title: 'menus.exception.500',
        icon: 'ri:server-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}