import BaseApi from '#/api/base-api';
import type {DictItem } from './dict-item-model';

/**
 * 字典项 API 类
 */
export class SystemDictItemApi extends BaseApi<DictItem> {
  constructor() {
    super('/system/dict-item');
  }
}