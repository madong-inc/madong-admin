import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';
import { SystemCrontabLogRow, SystemCrontabRow } from './crontab-model';



/**
 * 定时任务
 */
export class SystemCrontabApi extends BaseApi<SystemCrontabRow> {

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/dev/crontab');
  }



  /**
 * 启动
 * @param data
 * @returns
 */
  start(data: Record<string, any>) {
    return requestClient.post(`${this.baseUrl}/resume`, data);
  }

  /**
   * 恢复
   * @param data
   * @returns
   */
  resume(data: Record<string, any>) {
    return requestClient.put(`${this.baseUrl}/resume`, data);
  }

  /**
   * 暂停
   * @param data
   * @returns
   */
  pause(data: Record<string, any>) {
    return requestClient.put(`${this.baseUrl}/pause`, data);
  }

  /**
   * 立即执行
   * @param data
   * @returns
   */
  execute(data: Record<string, any>) {
    return requestClient.put(`${this.baseUrl}/execute`, data);
  }
}


/**
 * 定时任务日志
 */
export class SystemCrontabLogApi extends BaseApi<SystemCrontabLogRow> {

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    super('/dev/crontab-log');
  }

}