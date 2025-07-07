import BaseApi from '#/api/base-api';
import { requestClient } from '#/api/request';




export class SystemAuthApi extends BaseApi<any> {

    /**
     * 构造函数
     * @param baseUrl 
     */
    constructor() {
        super('/system/auth');
    }


    /**
     * 当前登录用户
     * @returns 
     */
    userInfo() {
        return requestClient.get<any>(`${this.baseUrl}/user-info`);
    }


    /**
     * 根据角色ID获取菜单ID集合
     * @param params
     */
    roleMenuIds(params: any) {
        return requestClient.get<any>(`${this.baseUrl}/role-menu-ids`, { params });
    }


    /**
     * 根据角色ID获取菜单ID集合
     * @param params
     */
    roleScopeIds(params: any) {
        return requestClient.get<any>(`${this.baseUrl}/role-scope-ids`, { params });
    }

    /**
     * 通过角色ID获取用户列表
     * @param params
     */
    userListByRoleId(params: any) {
        return requestClient.get<any>(`${this.baseUrl}/user-list-by-role-id`, { params });
    }

    /**
     * 获取用户列表-排除指定角色
     * @param params
     */
    userListExcludeRoleId(params: any) {
        return requestClient.get<any>(`${this.baseUrl}/user-list-exclude-role-id`, { params });
    }

    /**
     * 删除用户角色关系
     * @param data
     */
    removeUserRole(data: any) {
        return requestClient.post<any>(`${this.baseUrl}/remove-user-role`, data);
    }
    /**
     * 保存角色菜单关系
     * @param data
     */
    saveRoleMenu(data: any) {
        return requestClient.post<any>(`${this.baseUrl}/save-role-menu`, data);
    }
    /**
     * 添加用户角色关系
     * @param data
     */
    saveUserRole(data: any) {
        return requestClient.post<any>(`${this.baseUrl}/save-user-role`, data);
    }

    /**
     * 获取用户关联租户
     * @returns 
     */
    getUserTenant(){
        return requestClient.get<any>(`${this.baseUrl}/user-tenant`);
    }

    /**
     * 切换租户
     * @returns 
     */
    changeTenant(data:any){
        return requestClient.put<any>(`${this.baseUrl}/change-tenant`,data);
    }

    tenantGrant(data:any){
        return requestClient.put<any>(`${this.baseUrl}/tenant-grant`,data);
    }

    /**
     * 用户权限-包含菜单 按钮  接口
     * @returns 
     */
    getUserPermission(){
        return requestClient.get<any>(`${this.baseUrl}/permission`);
    }
}


/***********以下兼容前面应用 */

/**
 * 根据角色ID获取菜单ID集合
 * @param params
 */
export async function roleMenuIds(params: any) {
    return requestClient.get<any>('/system/auth/role-menu-ids', { params });
}

/**
 * 通过角色ID获取用户列表
 * @param params
 */
export async function userListByRoleId(params: any) {
    return requestClient.get<any>('/system/auth/user-list-by-role-id', { params });
}

/**
 * 获取用户列表-排除指定角色
 * @param params
 */
export async function userListExcludeRoleId(params: any) {
    return requestClient.get<any>('/system/auth/user-list-exclude-role-id', { params });
}

/**
 * 删除用户角色关系
 * @param data
 */
export async function removeUserRole(data: any) {
    return requestClient.post<any>('/system/auth/remove-user-role', data);
}
/**
 * 保存角色菜单关系
 * @param data
 */
export async function saveRoleMenu(data: any) {
    return requestClient.post<any>('/system/auth/save-role-menu', data);
}
/**
 * 添加用户角色关系
 * @param data
 */
export async function saveUserRole(data: any) {
    return requestClient.post<any>('/system/auth/save-user-role', data);
}
