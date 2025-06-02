<script setup lang="ts">
import { TabPane, Tabs } from 'ant-design-vue';

import BaseSetting from './components/base-setting.vue';
import OnlineDevice from './components/online-device.vue';
import SecureSetting from './components/secure-setting.vue';
import { $t } from '#/locale';

const settingList = [
  {
    component: BaseSetting,
    key: '1',
    name: $t('system.user.profile.base_setting.title'),
  },
  {
    component: SecureSetting,
    key: '2',
    name: $t('system.user.profile.secure_setting.title'),
  },
  {
    component: OnlineDevice,
    key: '3',
    name: $t('system.user.profile.online_device.title'),
  },
];
</script>

<template>
  <Tabs class="bg-background rounded-[var(--radius)] px-[16px] lg:flex-1">
    <TabPane v-for="item in settingList" :key="item.key" :tab="item.name">
      <component :is="item.component" v-bind="$attrs" />
    </TabPane>
  </Tabs>
</template>
