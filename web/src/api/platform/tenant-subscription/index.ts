import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";
import type { TenantSubscriptionRow } from "./model";



/**
 * 账套中心
 */
export class TenantSubscriptionApi extends BaseApi<TenantSubscriptionRow> {

    constructor() {
        super('/platform/tenant-subscription');
    }


    /**
     * 套餐授权ids
     * @param 
     * @returns 
     */

    packagePermissionIds(params?: Record<string, any>) {

        return requestClient.get(`${this.baseUrl}-permission-ids`, { params });
    }

    /**
     * 套餐关联租户ids
     * @param params 
     * @returns 
     */
    packageTenantIds(params?: Record<string, any>) {
        return requestClient.get(`${this.baseUrl}-tenant-ids`, { params });
    }


    /**
     * 授权权限
     * @param data 
     * @returns 
     */
    grantPermission(data:Record<string, any>){
        return requestClient.post(`${this.baseUrl}/grant-permission`, data);
    }

    /**
     * 套餐授权关联租户
     * @param data 
     * @returns 
     */
    grantTenant(data:Record<string,any>){
        return requestClient.post(`${this.baseUrl}/grant-tenant`, data);
    }

}


export type * from './model';

