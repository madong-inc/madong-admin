<script setup lang="ts">
import type { BasicButtonProps } from './button';

import { computed } from 'vue';

import { LoaderCircle } from '#/components/common/icons';
import { cn } from '#/components/common/utils';

import { Primitive } from 'radix-vue';

import { buttonVariants } from '../../ui';

interface Props extends BasicButtonProps {}

const props = withDefaults(defineProps<Props>(), {
  as: 'button',
  class: '',
  disabled: false,
  loading: false,
  size: 'default',
  variant: 'default',
});

const isDisabled = computed(() => {
  return props.disabled || props.loading;
});
</script>

<template>
  <Primitive
    :as="as"
    :as-child="asChild"
    :class="cn(buttonVariants({ variant, size }), props.class)"
    :disabled="isDisabled"
  >
    <LoaderCircle
      v-if="loading"
      class="text-md mr-2 size-4 flex-shrink-0 animate-spin"
    />
    <slot></slot>
  </Primitive>
</template>
