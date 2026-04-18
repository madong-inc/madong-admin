import { AppRouteRecord } from '@/types/router'

export const widgetsRoutes: AppRouteRecord = {
  name: 'DemoWidgets',
  path: '/demo-widgets',
  meta: {
    title: 'menus.widgets.title',
    icon: 'ri:apps-2-line',
    module: 'demo'
  },
  children: [
    {
      path: 'context-menu',
      name: 'DemoContextMenu',
      component: '/widgets/context-menu',
      meta: {
        title: 'menus.widgets.contextMenu',
        icon: 'ri:menu-2-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'count-to',
      name: 'DemoCountTo',
      component: '/widgets/count-to',
      meta: {
        title: 'menus.widgets.countTo',
        icon: 'ri:number-1',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'drag',
      name: 'DemoDrag',
      component: '/widgets/drag',
      meta: {
        title: 'menus.widgets.drag',
        icon: 'ri:drag-move-2-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'excel',
      name: 'DemoExcel',
      component: '/widgets/excel',
      meta: {
        title: 'menus.widgets.excel',
        icon: 'ri:file-excel-2-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'demo-fireworks',
      name: 'DemoFireworks',
      component: '/widgets/fireworks',
      meta: {
        title: 'menus.widgets.fireworks',
        icon: 'ri:fire-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'icon',
      name: 'DemoIcon',
      component: '/widgets/icon',
      meta: {
        title: 'menus.widgets.icon',
        icon: 'ri:emotion-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'image-crop',
      name: 'DemoImageCrop',
      component: '/widgets/image-crop',
      meta: {
        title: 'menus.widgets.imageCrop',
        icon: 'ri:crop-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'qrcode',
      name: 'DemoQrcode',
      component: '/widgets/qrcode',
      meta: {
        title: 'menus.widgets.qrcode',
        icon: 'ri:qr-code-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'text-scroll',
      name: 'DemoTextScroll',
      component: '/widgets/text-scroll',
      meta: {
        title: 'menus.widgets.textScroll',
        icon: 'ri:text-wrap',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'video',
      name: 'DemoVideo',
      component: '/widgets/video',
      meta: {
        title: 'menus.widgets.video',
        icon: 'ri:video-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'wang-editor',
      name: 'DemoWangEditor',
      component: '/widgets/wang-editor',
      meta: {
        title: 'menus.widgets.wangEditor',
        icon: 'ri:edit-box-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'watermark',
      name: 'DemoWatermark',
      component: '/widgets/watermark',
      meta: {
        title: 'menus.widgets.watermark',
        icon: 'ri:water-percent-line',
        keepAlive: true,
        module: 'demo'
      }
    },
    {
      path: 'image-selector',
      name: 'DemoImageSelector',
      component: '/widgets/image-selector',
      meta: {
        title: '图片选择器',
        icon: 'ri:image-line',
        keepAlive: true,
        module: 'demo'
      }
    }
  ]
}