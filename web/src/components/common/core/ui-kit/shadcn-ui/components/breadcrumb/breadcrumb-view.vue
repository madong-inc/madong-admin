<script lang="ts" setup>
import type {BreadcrumbProps} from './types';

import {useForwardPropsEmits} from 'radix-vue';

import BreadcrumbBackground from './breadcrumb-background.vue';
import Breadcrumb from './breadcrumb.vue';

interface Props extends BreadcrumbProps {
  class?: any;
}

const props = withDefaults(defineProps<Props>(), {});

const emit = defineEmits<{ select: [string] }>();

const forward = useForwardPropsEmits(props, emit);
</script>
<template>
  <Breadcrumb
      v-if="styleType === 'normal'"
      v-bind="forward"
      class="madong-breadcrumb"
  />
  <BreadcrumbBackground
      v-if="styleType === 'background'"
      v-bind="forward"
      class="madong-breadcrumb"
  />
</template>
<style lang="scss" scoped>
/** 修复全局引入Antd时，ol和ul的默认样式会被修改的问题 */
.madong-breadcrumb {
  :deep(ol),
  :deep(ul) {
    margin-bottom: 0;
  }
}
</style>