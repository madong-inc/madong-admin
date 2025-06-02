import { requestClient } from '#/api/request';
import { type ServerInfo } from './server-model';


/**
 * 性能监控
 */
export class ServerApi {

    /**
     * 基础路径
     */
    protected baseUrl: string;

    /**
     * 构造函数
     * @param baseUrl 
     */
    constructor() {
        this.baseUrl = '/monitor/server';
    }

    /**
     * 列表
     * @param params 
     * @returns 
     */
    list(params?: Record<string, any>): Promise<ServerInfo> {
        return requestClient.get(this.baseUrl, { params });
    }

}