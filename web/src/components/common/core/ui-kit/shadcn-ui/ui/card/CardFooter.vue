<script setup lang="ts">
import { cn } from '#/components/common/utils';

const props = defineProps<{
  class?: any;
}>();
</script>

<template>
  <div :class="cn('flex items-center p-6 pt-0', props.class)">
    <slot></slot>
  </div>
</template>
