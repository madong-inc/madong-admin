import type { RouteRecordStringComponent } from '#/components/core/types';

import { requestClient } from '#/api/request';

/**
 * 获取用户所有菜单
 */
export async function getAllMenusApi() {
  return requestClient.get<RouteRecordStringComponent[]>('/system/auth/user-menus');
}
