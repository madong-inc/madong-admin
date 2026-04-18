import { AppRouteRecord } from '@/types/router'

export const dashboardRoutes: AppRouteRecord = {
  name: 'DemoDashboard',
  path: '/demo-dashboard',
  meta: {
    title: 'menus.dashboard.title',
    icon: 'ri:pie-chart-line',
    roles: ['R_SUPER', 'R_ADMIN'],
    module: 'demo'
  },
  children: [
    {
      path: 'console',
      name: 'DemoConsole',
      component: '/dashboard/console',
      meta: {
        title: 'menus.dashboard.console',
        icon: 'ri:home-smile-2-line',
        keepAlive: false,
        fixedTab: false,
        module: 'demo'
      }
    },
    {
      path: 'analysis',
      name: 'DemoAnalysis',
      component: '/dashboard/analysis',
      meta: {
        title: 'menus.dashboard.analysis',
        icon: 'ri:align-item-bottom-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'ecommerce',
      name: 'DemoEcommerce',
      component: '/dashboard/ecommerce',
      meta: {
        title: 'menus.dashboard.ecommerce',
        icon: 'ri:bar-chart-box-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}