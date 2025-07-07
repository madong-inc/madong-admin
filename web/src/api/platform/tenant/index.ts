import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";
import type { TenantRow } from "./model";



/**
 * 账套中心
 */
export class TenantApi extends BaseApi<TenantRow> {

    constructor() {
        super('/platform/tenant');
    }

    /**
     * 租户套餐ids
     * @param 
     * @returns 
     */
    tenantSubscriptionIds(params?: Record<string, any>) {
        return requestClient.get(`${this.baseUrl}-subscription-ids`, { params });
    }


    /**
     * 授权套餐
     * @param data 
     * @returns 
     */
    grantSubscription(data:Record<string,any>){
        return requestClient.post(`${this.baseUrl}/grant-subscription`, data);
    }


}


export type * from './model';

