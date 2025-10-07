import type { UserInfo } from '#/components/core/types';

import { requestClient } from '#/api/request';


/**
 * 获取用户信息
 */
export async function getUserInfoApi() {
  return requestClient.get<UserInfo>('/system/auth/user-info');
}


/**
 * 图片验证码
 */
export async function captcha(data: any) {
  return requestClient.get('/system/captcha', data);
}


/**
 * 手机验证码
 * @param data
 * @returns
 */
export async function sendSmsCode(data: any) {
  return requestClient.post('/system/send-sms', data);
}

/**
 * 获取登录图片验证码是否启用标识
 */
export async function getCaptchaOpenFlag() {
  return requestClient.get('/system/get-captcha-open-flag');
}
