import type { Recordable, UserInfo } from '#/components/common/types';

import { ref } from 'vue';
import { useRouter } from 'vue-router';

import { DEFAULT_HOME_PATH, LOGIN_PATH } from '#/components/common/constants';
import { resetAllStores, useAccessStore, useUserStore } from '#/components/common/stores';

import { notification } from 'ant-design-vue';
import { defineStore } from 'pinia';

import { getAccessCodesApi, getUserInfoApi, loginApi, logoutApi } from '#/api';
import { $t } from '#/locale';

export const useAuthStore = defineStore('auth', () => {
  const accessStore = useAccessStore();
  const userStore = useUserStore();
  const router = useRouter();

  const loginLoading = ref(false);

  /**
   * 异步处理登录操作
   * Asynchronously handle the login process
   * @param params 登录表单数据
   */
  async function authLogin(
    params: Recordable<any>,
    onSuccess?: () => Promise<void> | void,
  ) {
    // 异步处理用户登录操作并获取 accessToken
    let userInfo: null | UserInfo = null;
    try {
      loginLoading.value = true;
      const { token, client_id } = await loginApi(params);
      const accessToken = token;
      const clientSide = client_id
      // 如果成功获取到 accessToken
      if (accessToken) {
        accessStore.setAccessToken(accessToken);

        await accessStore.setClientId(clientSide);


        // 获取用户信息并存储到 accessStore 中 解决打包后client_id缺失问题
        const [fetchUserInfoResult, accessCodes] = await Promise.all([
          fetchUserInfo(),
          getAccessCodesApi(),
        ]);

        userInfo = fetchUserInfoResult;

        userStore.setUserInfo(userInfo);
        accessStore.setAccessCodes(accessCodes);
        userStore.setAccessCodes(accessCodes);

        if (accessStore.loginExpired) {
          accessStore.setLoginExpired(false);
        } else {
          onSuccess
            ? await onSuccess?.()
            : await router.push(userInfo.homePath || DEFAULT_HOME_PATH);
        }

        if (userInfo?.real_name) {
          notification.success({
            description: `${$t('authentication.loginSuccessDesc')}:${userInfo?.real_name}`,
            duration: 3,
            message: $t('authentication.loginSuccess'),
          });
        }
      }
    } finally {
      loginLoading.value = false;
    }

    return {
      userInfo,
    };
  }

  async function logout(redirect: boolean = true) {
    try {
      await logoutApi();
    } catch {
      // 不做任何处理
    } finally {
      resetAllStores();
      accessStore.setLoginExpired(false);

      // 回登录页带上当前路由地址
      await router.replace({
        path: LOGIN_PATH,
        query: redirect
          ? {
            redirect: encodeURIComponent(router.currentRoute.value.fullPath),
          }
          : {},
      });

      // 强制刷新解决缓存问题-或者写一个重置路由的方法要不下次进入系统后端的路由无法添加到框架
      window.location.reload();
    }
  }

  async function fetchUserInfo() {
    let userInfo: null | UserInfo = null;
    userInfo = await getUserInfoApi();
    userStore.setUserInfo(userInfo);
    // accessStore.setPlayer(userInfo.ext.isPlayer || false);
    return userInfo;
  }


  async function fetchAccessCodes() {
    let accessCodes: Array<string> = [];
    accessCodes = await getAccessCodesApi();
    accessStore.setAccessCodes(accessCodes);
    return accessCodes;
  }
  function $reset() {
    loginLoading.value = false;
  }

  return {
    $reset,
    authLogin,
    fetchUserInfo,
    loginLoading,
    fetchAccessCodes,
    logout,
  };
});
