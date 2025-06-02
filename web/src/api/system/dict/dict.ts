import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import {DictOptions, Dict} from './dict-model';



/**
 * 系统字典 API
 */
export class SystemDictApi extends BaseApi<Dict> {

  constructor() {
    super('/system/dict');
  }

   async enumDictList(params: any): Promise<Dict[]> {
    return requestClient.get(`${this.baseUrl}/enum-dict-list`, {params});
  }

   async customDictList(params: any): Promise<Dict[]> {
    return requestClient.get(`${this.baseUrl}/custom-dict-list`, {params});
  }

   async getByDictType(params: any):Promise<DictOptions[]> {
    return requestClient.get(`${this.baseUrl}/get-by-dict-type`, {params});
  }
}
