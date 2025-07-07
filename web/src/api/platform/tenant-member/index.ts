import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";
import type{ User } from "#/api/system/user";
import type { TenantMemberRow } from "./model";



/**
 * 账套中心
 */
export class TenantMemberApi extends BaseApi<TenantMemberRow> {

    constructor() {
        super('/platform/tenant-member');
    }


    /**
     *
     * @param data 重置密码
     * @returns
     */
    resetPassword(data: Record<string, any>): Promise<User[]> {
        return requestClient.put(this.baseUrl + '/reset-password', data);
    }

    /**
     * 锁定用户
     * @param data 参数
     */
    locked(data: any) {
        return requestClient.put(this.baseUrl + '/locked', data);
    }

    /**
     * 取消锁定用户
     * @param data 参数
     */
    unLocked(data: any) {
        return requestClient.put(this.baseUrl + '/un-locked', data);
    }

}

export * from './tenant-member-admin';
export type * from './model';

