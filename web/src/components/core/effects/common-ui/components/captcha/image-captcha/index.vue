<script setup lang="ts">
import { onMounted, type PropType, ref, watch } from 'vue';

import { useVModel } from '@vueuse/core';

const props = defineProps({
  api: {
    type: Function as PropType<(params: any) => Promise<unknown>>,
    default: null,
  },
  uuidField: {
    type: String,
    default: 'uuid',
  },
  base64Field: {
    type: String,
    default: 'base64',
  },
  refresh: {
    type: Boolean,
    default: false,
  },
  params: {
    type: Object,
    default: () => {
      return {};
    },
  },
});
const emits = defineEmits(['update:modelValue', 'update:refresh']);

const mRefresh = useVModel(props, 'refresh', emits, {
  defaultValue: props.refresh,
  passive: true,
});

const uuid = ref();
const base64 = ref();
const fetch = () => {
  props.api?.(props.params).then((res: any) => {
    uuid.value = res[props.uuidField];
    base64.value = res[props.base64Field];
    emits('update:modelValue', uuid.value);
    mRefresh.value = false;
  });
};
onMounted(() => {
  fetch();
});
watch(
  () => mRefresh.value,
  (val) => {
    if (val) {
      fetch();
    }
  },
);
</script>
<template>
  <img :src="base64" @click="fetch" />
</template>
<style lang="less" scoped></style>
