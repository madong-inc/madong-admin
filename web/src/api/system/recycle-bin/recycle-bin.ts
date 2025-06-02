import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import { SystemRecycleBinRow } from './recycle-bin-model';


/**
 * 数据回收站
 */
export class SystemRecycleBinApi extends BaseApi<SystemRecycleBinRow> {

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/recycle-bin');
  }


  /**
   * 还原
   * @param data 
   * @returns 
   */
  restore(data: Record<string, any>) {
    return requestClient.put(this.baseUrl, data);
  }

}