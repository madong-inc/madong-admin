<script setup lang="ts">
import { cn } from '#/components/core/utils';

const props = defineProps<{
  class?: any;
}>();
</script>

<template>
  <p :class="cn('text-muted-foreground text-sm', props.class)">
    <slot></slot>
  </p>
</template>
