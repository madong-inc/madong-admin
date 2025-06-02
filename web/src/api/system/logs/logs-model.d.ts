
type Timestamp = number;  // Unix 时间戳
type DateTime = string;   // ISO 8601 格式字符串

/**
 * 登录日志
 */
export interface SystemLoginRecordRow {
  id: string; 
  user_name: string;
  ip: string;
  ip_location: string;
  os: string;
  browser: string;
  status: number;
  message: string | null;
  login_time: Timestamp;
  key: string;
  remark: string | null;
}


/**
 * 操作日志
 */
export interface SystemOperateRecordRow {
  id: string;
  name: string;
  app: string;
  ip: string;
  ip_location: string;
  browser: string;
  os: string;
  url: string;
  class_name: string;
  action: string;
  method: string;
  param: string;
  result: string;
  user_name: string;
}