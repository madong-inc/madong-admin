import { AppRouteRecord } from '@/types/router'

export const helpRoutes: AppRouteRecord = {
  name: 'DemoHelp',
  path: '/demo-help',
  meta: {
    title: 'menus.help.title',
    icon: 'ri:question-line',
    module: 'demo'
  },
  children: [
    {
      path: 'change-log',
      name: 'DemoChangeLog',
      component: '/change/log',
      meta: {
        title: 'menus.help.changeLog',
        icon: 'ri:file-list-3-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}