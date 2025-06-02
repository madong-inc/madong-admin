interface BasicUserInfo {
  /**
   * 头像
   */
  avatar: string;
  /**
   * 用户昵称
   */
  real_name: string;
  /**
   * 用户角色
   */
  roles?: string[];
  /**
   * 用户id
   */
  id: string;
  /**
   * 用户名
   */
  user_name: string;
}

export type {BasicUserInfo};