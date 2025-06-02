import BaseApi from '#/api/base-api';
import { SystemDataSourceRow } from './data-source-model';


/**
 * 数据中心
 */
export class SystemDataSourceApi extends BaseApi<SystemDataSourceRow> {

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/data-source');
  }



}