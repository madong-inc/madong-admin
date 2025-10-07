import type { RouteRecordRaw } from 'vue-router';

import type { MenuRecordRaw } from '#/components/core/@core/typings';

import { acceptHMRUpdate, defineStore } from 'pinia';

type AccessToken = null | string;

interface AccessState {
    /**
     * 权限码
     */
    accessCodes: string[];
    /**
     * 权限角色
     */
    accessRoles: string[];
    /**
     * 可访问的菜单列表
     */
    accessMenus: MenuRecordRaw[];
    /**
     * 可访问的路由列表
     */
    accessRoutes: RouteRecordRaw[];
    /**
     * 登录 accessToken
     */
    accessToken: AccessToken;
    /**
     * 是否已经检查过权限
     */
    accessChecked: boolean;
    /**
     * 登录是否过期
     */
    loginExpired: boolean;
    /**
     * 登录 accessToken
     */
    refreshToken: AccessToken;

    /**
     * 客户端id
     */
    clientId: string | null | undefined;
}

/**
 * @zh_CN 访问权限相关
 */
export const useAccessStore = defineStore('core-access', {
    actions: {
        getMenuByPath(path: string) {
            function findMenu(
                menus: MenuRecordRaw[],
                path: string,
            ): MenuRecordRaw | undefined {
                for (const menu of menus) {
                    if (menu.path === path) {
                        return menu;
                    }
                    if (menu.children) {
                        const matched = findMenu(menu.children, path);
                        if (matched) {
                            return matched;
                        }
                    }
                }
            }
            return findMenu(this.accessMenus, path);
        },
        setAccessCodes(codes: string[]) {
            this.accessCodes = codes;
        },
        setAccessRoles(roles: string[]) {
            this.accessRoles = roles;
        },
        setAccessMenus(menus: MenuRecordRaw[]) {
            this.accessMenus = menus;
        },
        setAccessRoutes(routes: RouteRecordRaw[]) {
            this.accessRoutes = routes;
        },
        setAccessToken(token: AccessToken) {
            this.accessToken = token;
        },
        setAccessChecked(accessChecked: boolean) {
            this.accessChecked = accessChecked;
        },
        setLoginExpired(loginExpired: boolean) {
            this.loginExpired = loginExpired;
        },
        setRefreshToken(token: AccessToken) {
            this.refreshToken = token;
        },
        //自定义添加
        setClientId(clientId: string | null | undefined) {
            this.clientId = clientId;
        },
    },
    persist: {
        // 持久化
        pick: ['accessToken', 'refreshToken', 'accessCodes', 'accessRoles','clientId'],
    },
    state: (): AccessState => ({
        accessCodes: [],
        accessRoles: [],
        accessMenus: [],
        accessRoutes: [],
        accessToken: null,
        accessChecked: false,
        loginExpired: false,
        refreshToken: null,
        clientId: null
    }),
});

// 解决热更新问题
const hot = import.meta.hot;
if (hot) {
    hot.accept(acceptHMRUpdate(useAccessStore, hot));
}
