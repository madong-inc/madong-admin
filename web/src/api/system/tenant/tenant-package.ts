import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";
import { SystemTenantPackageRow } from "./model";



/**
 * 租户套餐接口
 */
export class SystemTenantPackageApi extends BaseApi<SystemTenantPackageRow> {

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/tenant-package');
  }

  /**
   * 套餐授权详情
   * @param 
   * @returns 
   */

  packageMenuIds(params?: Record<string, any>) {

    return requestClient.get(`${this.baseUrl}-menu-ids`, { params });
  }
}