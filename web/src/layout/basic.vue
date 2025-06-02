<script lang="ts" setup>
import type { NotificationItem } from '../components/common/effects/layouts/widgets';

import { computed, onBeforeMount, onMounted, ref, watch } from 'vue';
import { useRouter } from "vue-router";

import { AuthenticationLoginExpiredModal } from '#/components/common-ui';
import { useWatermark } from '#/components/common/effects/hooks';
import {
  BasicLayout,
} from '../components/common/effects/layouts/basic';
import {
  LockScreen,
  Notification,
  UserDropdown,
} from '../components/common/effects/layouts/widgets'
import { preferences } from '#/components/common/core/preferences';
import { useAccessStore, useTabbarStore, useUserStore, useAuthStore } from '#/components/common/stores';
import { openWindow } from '#/components/common/utils';

import { $t } from '#/locale';
import LoginForm from '#/views/core/authentication/login.vue';

const { setMenuList } = useTabbarStore();
setMenuList([
  'close',
  'affix',
  'maximize',
  'reload',
  'open-in-new-window',
  'close-left',
  'close-right',
  'close-other',
  'close-all',
]);

const notifyStore = useNotifyStore();


const userStore = useUserStore();
const authStore = useAuthStore();
const accessStore = useAccessStore();
const { destroyWatermark, updateWatermark } = useWatermark();


import { BookOpenText } from "#/components/common/icons";
import { useNotifyStore } from '#/store';
import { message } from 'ant-design-vue';
import { SystemMessageApi } from '#/api/system/message';

const api = new SystemMessageApi();
const router = useRouter();
const menus = computed(() => [
  {
    handler: () => {
      router.push('/profile');
    },
    icon: BookOpenText,
    text: $t('system.user.profile.title'),
  },
]);


const avatar = computed(() => {
  return userStore.userInfo?.avatar ?? preferences.app.defaultAvatar;
});

async function handleLogout() {
  await authStore.logout(false);
}

/**
 * 所以消息
 */
function handleViewAll() {
   router.push('/system/message');
}

function handleClickLogo() {}

watch(
  () => preferences.app.watermark,
  async (enable) => {
    if (enable) {
      await updateWatermark({
        content: `${userStore.userInfo?.real_name} - ${userStore.userInfo?.real_name}`,
      });
    } else {
      destroyWatermark();
    }
  },
  {
    immediate: true,
  },
);

onBeforeMount(() => {
  if (preferences.app.watermark) {
    destroyWatermark();
  }
});



onMounted(() => {
  notifyStore.startListeningMessage();
  //延迟1秒预防ui没加载完成
  setTimeout(() => {
    api.notifyOnFirstLoginToAll({});
  }, 1000); 
});


</script>

<template>
  <BasicLayout
      @clear-preferences-and-logout="handleLogout"
      @click-logo="handleClickLogo"
  >
    <template #user-dropdown>
      <UserDropdown
        :avatar
        :menus
        :text="userStore.userInfo?.real_name"
        description="www.madong.tech"
        tag-text="PRO"
        @logout="handleLogout"
      />
    </template>
    <template #notification>
      <Notification
        :dot="notifyStore.showDot"
        :notifications="notifyStore.notifications"
        @clear="notifyStore.clearAllMessage"
        @make-all="notifyStore.setAllRead"
        @read="notifyStore.setRead"
        @view-all="handleViewAll"
      />
    </template>
    <template #extra>
      <AuthenticationLoginExpiredModal
        v-model:open="accessStore.loginExpired"
        :avatar
      >
        <LoginForm />
      </AuthenticationLoginExpiredModal>
    </template>
    <template #lock-screen>
      <LockScreen :avatar @to-login="handleLogout" />
    </template>
  </BasicLayout>
</template>
