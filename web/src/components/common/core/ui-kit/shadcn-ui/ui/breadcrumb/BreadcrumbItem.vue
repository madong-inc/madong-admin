<script lang="ts" setup>
import { cn } from '#/components/common/utils';

const props = defineProps<{
  class?: any;
}>();
</script>

<template>
  <li
    :class="
      cn('hover:text-foreground inline-flex items-center gap-1.5', props.class)
    "
  >
    <slot></slot>
  </li>
</template>
