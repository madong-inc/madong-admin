<script setup lang="ts">
import type { SelectOption } from '#/components/common/core/typings';

import { useSlots } from 'vue';

import { CircleHelp } from '#/components/common/icons';

import {
  NumberField,
  NumberFieldContent,
  NumberFieldDecrement,
  NumberFieldIncrement,
  NumberFieldInput,
  BasicTooltip,
} from '#/components/common/core/ui-kit/shadcn-ui';

defineOptions({
  name: 'PreferenceSelectItem',
});

withDefaults(
  defineProps<{
    disabled?: boolean;
    items?: SelectOption[];
    placeholder?: string;
    tip?: string;
  }>(),
  {
    disabled: false,
    placeholder: '',
    tip: '',
    items: () => [],
  },
);

const inputValue = defineModel<number>();

const slots = useSlots();
</script>

<template>
  <div
    :class="{
      'hover:bg-accent': !slots.tip,
      'pointer-events-none opacity-50': disabled,
    }"
    class="my-1 flex w-full items-center justify-between rounded-md px-2 py-1"
  >
    <span class="flex items-center text-sm">
      <slot></slot>

      <BasicTooltip v-if="slots.tip || tip" side="bottom">
        <template #trigger>
          <CircleHelp class="ml-1 size-3 cursor-help" />
        </template>
        <slot name="tip">
          <template v-if="tip">
            <p v-for="(line, index) in tip.split('\n')" :key="index">
              {{ line }}
            </p>
          </template>
        </slot>
      </BasicTooltip>
    </span>

    <NumberField v-model="inputValue" v-bind="$attrs" class="w-[165px]">
      <NumberFieldContent>
        <NumberFieldDecrement />
        <NumberFieldInput />
        <NumberFieldIncrement />
      </NumberFieldContent>
    </NumberField>
  </div>
</template>
