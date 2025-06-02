import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";
import type { SystemMessageRow } from "./model";



/**
 * 系统消息
 */
export class SystemMessageApi extends BaseApi<SystemMessageRow> {

    constructor() {
        super('/system/message');
    }


    /**
     * 更新
     * @param id 
     * @param data 
     * @returns 
     */
    updateRead(params: String | Array<String>) {
        const data = {
            id: params,
            status: 'read'
        };
        return requestClient.put(this.baseUrl + '/update-read', data);
    }

    emptyMessage(params: String | Array<String>) {
        const data = {
            id: params,
            status: 'deleted'
        }
        return requestClient.put(this.baseUrl + '/empty', data);
    }

    notifyOnFirstLoginToAll(data:any){
        return requestClient.post(this.baseUrl + '/notify-on-first-login-to-all', data);
    }

}


export type * from './model';

