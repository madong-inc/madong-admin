import { AppRouteRecord } from '@/types/router'

export const articleRoutes: AppRouteRecord = {
  name: 'DemoArticle',
  path: '/demo-article',
  meta: {
    title: 'menus.article.title',
    icon: 'ri:article-line',
    roles: ['R_SUPER', 'R_ADMIN'],
    module: 'demo'
  },
  children: [
    {
      path: 'list',
      name: 'DemoArticleList',
      component: '/article/list',
      meta: {
        title: 'menus.article.list',
        icon: 'ri:list-unordered',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'detail',
      name: 'DemoArticleDetail',
      component: '/article/detail',
      meta: {
        title: 'menus.article.detail',
        icon: 'ri:file-text-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'publish',
      name: 'DemoArticlePublish',
      component: '/article/publish',
      meta: {
        title: 'menus.article.publish',
        icon: 'ri:edit-line',
        keepAlive: false,
        module: 'demo'
      }
    },
    {
      path: 'comment',
      name: 'DemoArticleComment',
      component: '/article/comment',
      meta: {
        title: 'menus.article.comment',
        icon: 'ri:message-3-line',
        keepAlive: false,
        module: 'demo'
      }
    }
  ]
}