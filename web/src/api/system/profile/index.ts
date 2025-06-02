import { requestClient } from '#/api/request';
import type{ UpdateAvatarParam, UpdatePasswordParam, UpdateUserInfoParam, UserSession } from './model';


/**
 * 个人中心接口
 */
export class SystemProfileApi {

  /**
 * 基础路径
 */
  protected baseUrl: string;

  /**
   * 构造函数
   * @param baseUrl 
   */
  constructor() {
    this.baseUrl = '/system/profile'
  }


  /**
 * 修改头像-个人
 * @param data 参数
 */
  updateAvatar(data: UpdateAvatarParam) {
    return requestClient.put(this.baseUrl + '/update-avatar', data);
  }

  /**
   * 更新用户信息-个人
   * @param data 参数
   */
  updateInfo(data: UpdateUserInfoParam) {
    return requestClient.put(this.baseUrl + '/update-info', data);
  }

  /**
   * 更新用户密码-个人
   * @param data 参数
   */
  updatePwd(data: UpdatePasswordParam) {
    return requestClient.put(this.baseUrl + '/update-pwd', data);
  }


  /**
   * 我的在线设备
   * @param data 参数
   */
  onlineDevice(params?: Record<string, any>):Promise<UserSession> {
    return requestClient.get(this.baseUrl + '/online-device', { params });
  }


  /**
   * 踢用户下线
   * @param id 
   * @returns 
   */
  kickoutByTokenValue(id: string | number, data?: Record<string, any>): Promise<any> {
    return requestClient.delete(`${this.baseUrl + '/kickout-by-token-value'}/${id}`, { data });
  }

}