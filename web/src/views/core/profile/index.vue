<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

import { Page } from '#/components/core/effects/common-ui';
import { useUserStore } from '#/components/core/stores';

import { SystemProfileApi } from '#/api/system/profile';
import { useAuthStore } from '#/store';

import { emitter } from './mitt';
import ProfilePanel from './profile-panel.vue';
import SettingPanel from './setting-panel.vue';
import { SystemAuthApi } from '#/api/system/auth';
import { User } from '#/api/system/user';


const api =new SystemProfileApi();
const authApi = new SystemAuthApi();

const profile = ref<User>();
async function loadProfile() {
  const resp = await authApi.userInfo();
  profile.value = resp;
}

onMounted(loadProfile);

const authStore = useAuthStore();
const userStore = useUserStore();

async function handleUploadFinish() {
  // 重新加载用户信息
  await loadProfile();
  // 更新store
  const userInfo = await authStore.fetchUserInfo();
  userStore.setUserInfo(userInfo);
}

onMounted(() => emitter.on('updateProfile', loadProfile));
onUnmounted(() => emitter.off('updateProfile'));
</script>

<template>
  <Page>
    <div class="flex flex-col gap-[16px] lg:flex-row">
      <!-- 左侧 -->
      <ProfilePanel :profile="profile" @upload-finish="handleUploadFinish" />
      <!-- 右侧 -->
      <SettingPanel
        v-if="profile"
        :profile="profile"
        class="flex-1 overflow-hidden"
      />
    </div>
  </Page>
</template>
