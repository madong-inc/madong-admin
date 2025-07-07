<script setup lang="ts">
import { ref } from 'vue';

import { useDrawer } from '#/components/common-ui';

import CrontabLogIndex from '../../crontab-log/index.vue';

const record = ref();
const [Drawer, DrawerApi] = useDrawer({
  onOpenChange(isOpen) {
    record.value = isOpen ? DrawerApi.getData()?.record : {};
  },
});
defineExpose(DrawerApi);
</script>
<template>
  <Drawer :footer="false" class="w-[60%]" title="执行日志">
    <CrontabLogIndex v-if="record.id" :crontab_id="record.id"/>
  </Drawer>
</template>
<style lang="less" scoped></style>
