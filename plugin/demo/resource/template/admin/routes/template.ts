import { AppRouteRecord } from '@/types/router'

export const templateRoutes: AppRouteRecord = {
  name: 'DemoTemplate',
  path: '/demo-template',
  meta: {
    title: 'menus.template.title',
    icon: 'ri:layout-3-line',
    roles: ['R_SUPER', 'R_ADMIN'],
    module: 'demo'
  },
  children: [
    {
      path: 'banners',
      name: 'DemoBanners',
      component: '/template/banners',
      meta: {
        title: 'menus.template.banners',
        icon: 'ri:image-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'calendar',
      name: 'DemoCalendar',
      component: '/template/calendar',
      meta: {
        title: 'menus.template.calendar',
        icon: 'ri:calendar-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'cards',
      name: 'DemoCards',
      component: '/template/cards',
      meta: {
        title: 'menus.template.cards',
        icon: 'ri:card-view',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'charts',
      name: 'DemoCharts',
      component: '/template/charts',
      meta: {
        title: 'menus.template.charts',
        icon: 'ri:bar-chart-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'chat',
      name: 'DemoChat',
      component: '/template/chat',
      meta: {
        title: 'menus.template.chat',
        icon: 'ri:chat-1-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'map',
      name: 'DemoMap',
      component: '/template/map',
      meta: {
        title: 'menus.template.map',
        icon: 'ri:map-pin-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'pricing',
      name: 'DemoPricing',
      component: '/template/pricing',
      meta: {
        title: 'menus.template.pricing',
        icon: 'ri:price-tag-3-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}