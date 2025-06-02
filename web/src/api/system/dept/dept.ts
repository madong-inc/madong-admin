import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import { DeptTree, SystemDept } from './dept-model';

/**
 * 部门管理接口
 */
export class SystemDeptApi extends BaseApi<SystemDept> {


  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/system/dept');
  }


    /**
     * 左侧菜单树
     * @param params 
     * @returns 
     */
    getTree(args?: Record<string, any>): Promise<DeptTree[]> {
        const params={
          ...args,
          format:'tree'
        }
        return requestClient.get<DeptTree[]>(this.baseUrl, { params });
    }

}