import BaseApi from "#/api/base-api";
import { requestClient } from "#/api/request";
import type{ SystemNoticeRow } from "./model";



/**
 * 抄送待阅接口
 */
export class SystemNoticeApi extends BaseApi<SystemNoticeRow> {

    constructor() {
        super('/system/notice');
    }



    test() {
        return requestClient.post(this.baseUrl+'/test');
    }

}


export type * from './model';

