import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import type { RoleItem } from './role-model';


/**
 * 路由管理接口
 */
export class SystemRoleApi extends BaseApi<RoleItem> {


  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/role');
  }

    /**
     * 分配数据权限
     * @param data 
     * @returns 
     */
    dataScope(data: Record<string, any>) : Promise<RoleItem> {
      return requestClient.put<any>(`${this.baseUrl}/data-scope`, data);
    }

}





