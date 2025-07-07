import BaseApi from "#/api/base-api";
import type { DbSettingRow } from "./model";



/**
 * 网关限制接口
 */
export class DbSettingApi extends BaseApi<DbSettingRow> {

    constructor() {
        super('/platform/db');
    }

}


export type * from './model';

