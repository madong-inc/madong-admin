import { SiteConfig } from './../api/system/config/config-model.d';
import type {
  ComponentRecordType,
  GenerateMenuAndRoutesOptions,
} from '#/components/common/types';

import { generateAccessible } from '#/components/common/effects/access';
import { preferences } from '#/components/common/core/preferences';

import { message } from 'ant-design-vue';

import { BasicLayout, IFrameView } from '#/layout';
import { $t } from '#/locale';
import { localRoutesList } from './routes/local';
import { cloneDeep } from '#/components/common/utils';
import { getAllMenusApi } from '#/api/core/menu';
import { getAccessCodesApi } from '#/api';
import { useAccessStore, useUserStore } from '#/components/common/stores';
import { SystemConfigApi } from '#/api/system/config';
import { useSiteConfigStore } from '#/store/modules/site-config';

const forbiddenComponent = () => import('#/views/core/fallback/forbidden.vue');
const NotFoundComponent = () => import('#/views/core/fallback/not-found.vue');




async function generateAccess(options: GenerateMenuAndRoutesOptions) {
  const pageMap: ComponentRecordType = import.meta.glob('../views/**/*.vue');

  const layoutMap: ComponentRecordType = {
    BasicLayout,
    IFrameView,
    NotFoundComponent,
  };

  return await generateAccessible(preferences.app.accessMode, {
    ...options,
    fetchMenuListAsync: async () => {
      message.loading({
        content: `${$t('common.loadingMenu')}...`,
        duration: 1.5,
      });
        const accessStore = useAccessStore();
        const userStore = useUserStore();
        const siteConfigStore= useSiteConfigStore();


        /**
         * 合并请求提速50%
         */
        const [backList, accessCodes,siteSetting] = await Promise.all([
          getAllMenusApi(),
          getAccessCodesApi(),
          (new SystemConfigApi()).get({ group_code: "site_setting" })
        ]);
        
        siteConfigStore.setSiteConfig(siteSetting);

        //按钮权限
        accessStore.setAccessCodes(accessCodes);
        userStore.setAccessCodes(accessCodes);

      //追加前端定义的静态路由
      return [...backList,...cloneDeep(localRoutesList)];
    },
    // 可以指定没有权限跳转403页面
    forbiddenComponent,
    // 如果 route.meta.menuVisibleWithForbidden = true
    layoutMap,
    pageMap,
  });
}

export { generateAccess };
