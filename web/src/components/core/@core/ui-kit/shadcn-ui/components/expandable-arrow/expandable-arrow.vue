<script lang="ts" setup>
import { ChevronDown } from '#/components/core/icons';
import { cn } from '#/components/core/utils';

const props = defineProps<{
  class?: string;
}>();

// 控制箭头展开/收起状态
const collapsed = defineModel({ default: false });
</script>

<template>
  <div
    :class="cn('madong-link inline-flex items-center', props.class)"
    @click="collapsed = !collapsed"
  >
    <slot :is-expanded="collapsed">
      {{ collapsed }}
      <!-- <span>{{ isExpanded ? '收起' : '展开' }}</span> -->
    </slot>
    <div
      :class="{ 'rotate-180': !collapsed }"
      class="transition-transform duration-300"
    >
      <slot name="icon">
        <ChevronDown class="size-4" />
      </slot>
    </div>
  </div>
</template>
