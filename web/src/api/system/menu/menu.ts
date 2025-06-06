import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import { SystemMenuRow } from './menu-model';

/**
 * 菜单接口类
 */
export class SystemMenuApi extends BaseApi<SystemMenuRow> {

  constructor() {
    super('/system/menu');
  }

  /** 徽标颜色集合 */
  BadgeVariants = [
    'default',
    'destructive',
    'primary',
    'success',
    'warning',
  ] as const;

  /** 徽标类型集合 */
  BadgeTypes = [
    'dot',
    'normal'
  ] as const;

  /** 菜单类型集合 */
  MenuTypes = [
    1,
    2,
    3,
    4,
    5,
    6
  ] as const;


  /**
   * 获取菜单树
   */
  fetchPermissionTreeExclNonPackageIds(params?: Record<string, any>) {
        return requestClient.get(`${this.baseUrl}-tree`, { params });
  }

  /**
   * 批量创建菜单
   * @param data 批量菜单数据
   * @returns 请求结果
   */
  batchStore(data: Record<string, any>) {
    return requestClient.post(`${this.baseUrl}/batch-store`, data);
  }
}
