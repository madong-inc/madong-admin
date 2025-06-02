import { requestClient } from '#/api/request';


/**
 * 基础接口
 */
class BaseApi<T = any> {


    /**
     * 基础路径
     */
    protected baseUrl: string;


    /**
     * 不允许请求的方法
     */
    protected forbiddenMethods: string[] = [];

    /**
     * 允许请求的方法
     */
    protected allowedMethods: string[] = [];

    /**
     * 构造函数
     * @param baseUrl 
     */
    constructor(baseUrl: string) {
        this.baseUrl = baseUrl;
    }

    /**
     * 列表
     * @param params 
     * @returns 
     */
    list(params?: Record<string, any>): Promise<T[]> {
        this.checkMethod('list');
        return requestClient.get<T[]>(this.baseUrl, { params });
    }

    /**
     * 详情
     * @param id 
     * @returns 
     */
    get(id: string | number): Promise<T> {
        this.checkMethod('get');
        return requestClient.get<T>(`${this.baseUrl}/${id}`);
    }

    /**
     * 添加
     * @param data 
     * @returns 
     */
    create(data: Omit<T, 'id'>): Promise<T> {
        this.checkMethod('create');
        return requestClient.post<T>(this.baseUrl, data);
    }

    /**
     * 更新
     * @param id 
     * @param data 
     * @returns 
     */
    update(data: Partial<T>): Promise<T> {
        this.checkMethod('update');
        return requestClient.put<T>(this.baseUrl, data);
    }

    /**
     * 删除
     * @param id 
     * @returns 
     */
    remove(id: string | number, data?: Record<string, any>): Promise<T> {
        this.checkMethod('remove');
        return requestClient.delete(`${this.baseUrl}/${id}`, { data });
    }

    /**
     * 导出excel
     * @param data 
     * @returns 
     */
    export(data: any) {
        return requestClient.post(this.baseUrl + '/export', data);
    }

    /**
     * 更新状态
     * @param id 
     * @param data 
     * @returns 
     */
    changStatus(data: Partial<T>): Promise<T> {
        this.checkMethod('changStatus');
        return requestClient.put<T>(`${this.baseUrl}/change-status`, data);
    }


    /**
     * 检查方法是否被禁止或允许
     * @param method 
     */
    checkMethod(method: string): void {
        if (this.forbiddenMethods.includes(method)) {
            throw new Error(`${method} method is forbidden.`);
        }
        if (this.allowedMethods.length > 0 && !this.allowedMethods.includes(method)) {
            throw new Error(`${method} method is not allowed.`);
        }
    }
}

export default BaseApi;