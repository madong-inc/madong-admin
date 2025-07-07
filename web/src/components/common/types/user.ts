import type { BasicUserInfo } from '#/components/common/core/typings';

/** 用户信息 */
interface UserInfo extends BasicUserInfo {
  /**
   * 用户描述
   */
  desc: string;
  /**
   * 首页地址
   */
  homePath: string;

  /**
   * accessToken
   */
  access_token: string;

  refresh_token:string;
}

export type { UserInfo };
