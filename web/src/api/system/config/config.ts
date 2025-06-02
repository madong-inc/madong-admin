import { requestClient } from '#/api/request';



/**
 * 配置接口
 */
export class SystemConfigApi {

  /**
 * 基础路径
 */
  protected baseUrl: string;

  /**
* 构造函数
*/
  constructor() {
    this.baseUrl = '/system/config';
  }


  /**
   * 配置详情
   * @param params 
   * @returns 
   */
  get(params?: Record<string, any>) {
    return requestClient.get(`${baseUrl}/info`, { params });
  }

  /**
   * 
   * @param data 保存配置
   * @returns 
   */
  save(data: Record<string, any>) {
    return requestClient.put(baseUrl, data);
  }

}



/**
 * 
 * 以下兼容早期版本
 */
const baseUrl = '/system/config';

export const info = (params?: Record<string, any>) => {
  return requestClient.get(`${baseUrl}/info`, { params });
}

/**
 * 保存配置
 * @param data
 * @returns
 */
export const save = (data: Record<string, any>) => {
  return requestClient.post(baseUrl, data);
}
