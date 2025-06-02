<script setup lang="ts">
import { ref } from 'vue';

import { useDrawer } from '#/components/common-ui';

import DictItemIndex from '#/views/system/dict-item/index.vue';
import { $t } from '#/locale';

const record = ref();
const [Drawer, drawerApi] = useDrawer({
  onOpenChange(isOpen) {
    record.value = isOpen ? drawerApi.getData()?.record : {};
  },
});

/**
 * 标题
 */
function getTitle():string{
  return $t('system.dict_item.list.title');
}

</script>
<template>
  <Drawer :footer="false" class="w-[60%]" :title=getTitle()>
    <DictItemIndex v-if="record.id" :dict_id="record.id" :dict_code="record.code"/>
  </Drawer>
</template>
<style lang="less" scoped></style>
