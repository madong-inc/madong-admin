import { baseRequestClient, requestClient } from '#/api/request';

export namespace AuthApi {
  /** 登录接口参数 */
  export interface LoginParams {
    password?: string;
    user_name?: string;
    uuid?:string;
    code?:string;
    mobile_phone?:number|string;
  }

  /** 登录接口返回值 */
  export interface LoginResult {
    access_token: string;
    user_id?:string|number;
    client_id?:string|null|undefined;
    expires_time?:string|number;
    refresh_token:string;
  }

  export interface RefreshTokenResult {
    data: string;
    code: number;
  }

  export interface AccountSet {
    id: number|string;
    tenant_id:string|number;
    name: string;
  }

  export interface AccountSetResponse {
    is_tenant_mode_enabled: boolean; // 是否开启账套模式
    list: AccountSet[]; // 账套列表
  }

}

/**
 * 登录
 */
export async function loginApi(data: AuthApi.LoginParams) {
  return requestClient.post<AuthApi.LoginResult>('/system/login', data);
}

/**
 * 刷新accessToken
 */
export async function refreshTokenApi() {
  return baseRequestClient.post<AuthApi.RefreshTokenResult>('/system/auth/refresh', {
    withCredentials: true,
  });
}

/**
 * 退出登录
 */
export async function logoutApi() {
  return baseRequestClient.post('/system/logout', {
    withCredentials: true,
  });
}

/**
 * 获取用户权限码
 */
export async function getAccessCodesApi() {
  return requestClient.get<string[]>('/system/auth/perm-code');
}


/**
 * 获取账套列表
 * @returns
 */
export async function getAccountSet() {
  return requestClient.get<AuthApi.AccountSetResponse>('/platform/account-sets');
}



