<script setup lang="ts">
import { ref } from 'vue';

import { useDrawer } from '#/components/common-ui';

import UserTenantIndex from '#/views/platform/tenant-member-admin/index.vue';
import { $t } from '#/locale';

const record = ref();
const [Drawer, drawerApi] = useDrawer({
  onOpenChange(isOpen) {
    record.value = isOpen ? drawerApi.getData()?.record : {};
    console.log(record.value)

  },
});

/**
 * 标题
 */
function getTitle():string{
  return $t('platform.tenant_member_admin.list.title');
}

</script>
<template>
  <Drawer :footer="false" class="w-[60%]" :title=getTitle()>
    <UserTenantIndex v-if="record.id" :id="record.id"/>
  </Drawer>
</template>
<style lang="less" scoped></style>
