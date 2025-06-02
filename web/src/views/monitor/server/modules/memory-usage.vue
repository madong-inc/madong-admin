<script setup lang="ts">
import { onMounted, reactive, ref, watch } from "vue";
import {Card} from "ant-design-vue";

import type { VxeGridListeners, VxeGridProps } from "#/adapter";
import { useVxeGrid } from "#/adapter";

import { gridMemorySchemas as gridSchemas } from "../data";

const props = defineProps({
  data: { type: Array, default: [] },
});

const gridOptions: VxeGridProps<any> = {
  ...gridSchemas,
  toolbarConfig: {
    ...gridSchemas.toolbarConfig,
    slots: {
      buttons: "toolbar-buttons",
    },
    custom: {
      // 自定义列-图标
      icon: "vxe-icon-menu",
    },
  },
  border: "inner",
  height:'296',
  keepSource: true,
  data: [],
  pagerConfig: {
    enabled: false,
  },
  proxyConfig: {},
};

const [CupGrid, gridApi] = useVxeGrid({
  gridOptions,
});

watch(
  () => props.data,
  (newData) => {
    gridApi.setGridOptions({ data: newData });
  },
  { deep: true }
);

onMounted(async () => {
  gridApi.setGridOptions({ data: props.data });
});
</script>
<template>
  <Card size="small" class="no-padding">
    <template #title>
      <div class="flex items-center justify-start gap-[6px]">
        <span>{{ $t('monitors.server.memory.title') }}</span>
      </div>
    </template>
    <CupGrid />
  </Card>
</template>
