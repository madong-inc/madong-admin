import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";

import { SystemTenantRow } from "./model";


/**
 * 租户管理接口
 */
export class SystemTenantApi extends BaseApi<SystemTenantRow> {

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/tenant');
  }

  /**
   * 列表
   * @param params 
   * @returns 
   */
  tenantUser(params?: Record<string, any>): Promise<any[]> {
    this.checkMethod('list');
    return requestClient.get(`${this.baseUrl}-user`, { params });
  }
}