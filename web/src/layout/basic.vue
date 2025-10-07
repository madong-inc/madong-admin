<script lang="ts" setup>
import type { NotificationItem } from "../components/core/effects/layouts/widgets";

import { computed, onBeforeMount, onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";

import { AuthenticationLoginExpiredModal, useModal } from "#/components/core/effects/common-ui";
import { useWatermark } from "#/components/core/effects/hooks";
import { BasicLayout } from "../components/core/effects/layouts/basic";
import {
  LockScreen,
  Notification,
  UserDropdown,
} from "../components/core/effects/layouts/widgets";
import { preferences } from "#/components/core/@core/preferences";
import {
  useAccessStore,
  useTabbarStore,
  useUserStore,
  useAuthStore,
} from "#/components/core/stores";
import { openWindow } from "#/components/core/utils";

import { $t } from "#/locale";
import LoginForm from "#/views/core/authentication/login.vue";
import Terminal from "./modules/terminal.vue";
import { BasicIconButton } from "#/components/core/@core/ui-kit/shadcn-ui";

const { setMenuList } = useTabbarStore();
setMenuList([
  "close",
  "affix",
  "maximize",
  "reload",
  "open-in-new-window",
  "close-left",
  "close-right",
  "close-other",
  "close-all",
]);

const notifyStore = useNotifyStore();

const userStore = useUserStore();
const authStore = useAuthStore();
const accessStore = useAccessStore();
const { destroyWatermark, updateWatermark } = useWatermark();

import { BookOpenText} from "#/components/core/icons";
import { useNotifyStore } from "#/store";
import { message } from "ant-design-vue";
import { CodeOutlined } from "@ant-design/icons-vue";
import { SystemMessageApi } from "#/api/system/message";

const api = new SystemMessageApi();
const router = useRouter();
const menus = computed(() => [
  {
    handler: () => {
      router.push("/profile");
    },
    icon: BookOpenText,
    text: "个人中心",
  },
]);

const [TerminalModal, terminalApi] = useModal({
  connectedComponent: Terminal,
});


const avatar = computed(() => {
  return userStore.userInfo?.avatar ?? preferences.app.defaultAvatar;
});

async function handleLogout() {
  await authStore.logout(false);
}

function onTerminal() {
  terminalApi.setData({}).open();
}


/**
 * 切换租户刷新操作
 */
function handleReload(){
  window.location.reload();
}

/**
 * 所以消息
 */
function handleViewAll() {
  router.push("/system/message");
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
  }
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
  <BasicLayout @clear-preferences-and-logout="handleLogout" @click-logo="handleClickLogo">
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

    <template #terminal>
      <BasicIconButton @click="onTerminal" v-access:code="['admin', 'system:terminal']">
        <CodeOutlined class="text-foreground size-4" />
      </BasicIconButton>
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
      <AuthenticationLoginExpiredModal v-model:open="accessStore.loginExpired" :avatar>
        <LoginForm />
      </AuthenticationLoginExpiredModal>
    </template>
    <template #lock-screen>
      <LockScreen :avatar @to-login="handleLogout" />
    </template>
  </BasicLayout>
  <TerminalModal />
</template>
