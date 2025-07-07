import BaseApi from '#/api/base-api';
import { TenantRow } from '#/api/platform/tenant';
import { requestClient } from '#/api/request';
import { SystemDeptRow } from '../../system/dept';
import { SystemPostRow } from '../../system/post';
import type { TenantMemberAdminRow } from './model';



export class TenantMemberAdminApi extends BaseApi<TenantMemberAdminRow> {

  constructor() {
    super('/platform/tenant-admin');
  }

  /**
   * 租户列表
   * @param params 
   * @returns 
   */
  getTenant(params?: Record<string, any>): Promise<TenantRow[]> {
    return requestClient.get<TenantRow[]>(`${this.baseUrl}/tenants`, { params });
  }

  /**
   * 租户部门列表
   * @param params 
   * @returns 
   */
  getTenantDept(params?: Record<string, any>): Promise<SystemDeptRow[]> {
    return requestClient.get<SystemDeptRow[]>(`${this.baseUrl}/depts`, { params });
  }

  /**
* 租户职位列表
* @param params 
* @returns 
*/
  getTenantPost(params?: Record<string, any>): Promise<SystemPostRow[]> {
    return requestClient.get<SystemPostRow[]>(`${this.baseUrl}/posts`, { params });
  }

  /**
* 租户角色列表
* @param params 
* @returns 
*/
  getTenantRoles(params?: Record<string, any>): Promise<SystemPostRow[]> {
    return requestClient.get<SystemPostRow[]>(`${this.baseUrl}/roles`, { params });
  }

}




