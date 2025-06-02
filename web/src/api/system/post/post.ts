import BaseApi from '#/api/base-api';
import { SystemPost } from './post-model';


/**
 * 部门接口
 */
export class SystemPostApi extends BaseApi<SystemPost>{
  constructor() {
    super('/system/post');
  }
}