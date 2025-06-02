import { requestClient } from '#/api/request';
import { RedisMonitorData } from './redis-model';


export class RedisApi {

    /**
     * 基础路径
     */
    protected baseUrl: string;

    /**
     * 构造函数
     * @param baseUrl 
     */
    constructor() {
        this.baseUrl = '/monitor/redis';
    }

    /**
     * 列表
     * @param params 
     * @returns 
     */
    list(params?: Record<string, any>): Promise<RedisMonitorData> {
        return requestClient.get(this.baseUrl, { params });
    }

}