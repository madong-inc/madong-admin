import BaseApi from "./base-api";
import { requestClient } from "./request";

/**
 * 部门管理接口
 */
export class DownloadApi extends BaseApi<any> {


    /**
     * 构造函数
     * @param baseUrl 
     */
    constructor() {
      super('/export');
    }
  
  
      /**
       * 导出Excel
       * @param params 
       * @returns 
       */
      excelDownload(params: any) {
          return requestClient.get(this.baseUrl+'/download',{params});
      }
  
  }