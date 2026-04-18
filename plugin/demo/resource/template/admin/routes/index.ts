import { AppRouteRecord } from '@/types/router'
import { dashboardRoutes } from './dashboard'
import { templateRoutes } from './template'
import { articleRoutes } from './article'
import { examplesRoutes } from './examples'
import { systemRoutes } from './system'
import { widgetsRoutes } from './widgets'
import { exceptionRoutes } from './exception'
import { resultRoutes } from './result'
import { safeguardRoutes } from './safeguard'
import { helpRoutes } from './help'
import { RoutesAlias } from '@/router/routesAlias'
import { $t } from '@/locales'

export default {
  path: '/demo-playground',
  name: 'DemoPlayground',
  component: RoutesAlias.Layout,
  meta: {
    title: $t('demo.demo.playground.title'),
    icon: 'ri:play-circle-line',
    module: 'demo'
  },
  children: [
    dashboardRoutes,
    templateRoutes,
    articleRoutes,
    examplesRoutes,
    systemRoutes,
    widgetsRoutes,
    exceptionRoutes,
    resultRoutes,
    safeguardRoutes,
    helpRoutes
  ]
} as AppRouteRecord