export interface UpdatePasswordParam {
  old_password: string;
  new_password: string;
}


export interface UpdateUserInfoParam {
  email: string;
  mobile_phone: number,
  sex: number,
  id: string;
  real_name: string;
}


export interface UpdateAvatarParam{
  avatar:string;
}

/**
 * 用户会话信息类型定义 
 * @日期 2025-05-08 09:27 
 */
export interface UserSession {
  id: string;
  user_name: string;
  ip: string;
  ip_location: string;
  os: string;
  browser: string;
  status: number;
  message: string;
  login_time: number;
  key: string;
  created_at: string;
  expires_at: string;
  updated_at: string|null;
  deleted_at: number | null;
  remark: string | null;
  created_date: string;
  updated_date: string;
}
 