import { AppRouteRecord } from '@/types/router'

export const resultRoutes: AppRouteRecord = {
  name: 'DemoResult',
  path: '/demo-result',
  meta: {
    title: 'menus.result.title',
    icon: 'ri:checkbox-circle-line',
    module: 'demo'
  },
  children: [
    {
      path: 'success',
      name: 'DemoSuccess',
      component: '/result/success',
      meta: {
        title: 'menus.result.success',
        icon: 'ri:checkbox-circle-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'fail',
      name: 'DemoFail',
      component: '/result/fail',
      meta: {
        title: 'menus.result.fail',
        icon: 'ri:close-circle-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}