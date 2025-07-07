import BaseApi from "#/api/base-api";
import type { GatewayLimiterRow,GatewayBlacklistRow } from "./model";



/**
 * 网关限制接口
 */
export class GatewayLimiterApi extends BaseApi<GatewayLimiterRow> {

    constructor() {
        super('/dev/gateway/limiter');
    }

}
export class GatewayBlacklistApi extends BaseApi<GatewayBlacklistRow> {

    constructor() {
        super('/dev/gateway/blacklist');
    }

}


export type * from './model';

