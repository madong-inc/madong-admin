import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import { RouteItem } from './rule-model';


/**
 * 路由管理接口
 */
export class SystemRuleApi extends BaseApi<RouteItem> {


  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/rule');
  }


  /**
 * 列表
 * @param params 
 * @returns 
 */
  list(params?: Record<string, any>): Promise<RouteItem[]> {
    return requestClient.get<RouteItem[]>(`system/rule-list`, { params });
  }

  /**
   * 类型
   * @param params 
   * @returns 
   */
  cate(params?: Record<string, any>): Promise<any[]> {
    return requestClient.get(`system/rule-cate`, { params });
  }


  /**
   * 同步
   * @param data 
   * @returns 
   */
  sync(data: any): Promise<void> {
    return requestClient.post(`system/rule-sync`, data);
  }

}