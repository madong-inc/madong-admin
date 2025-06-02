<script setup lang="ts">
import type { SelectOption } from '#/components/common/core/typings';

import { useSlots } from 'vue';

import { CircleHelp } from '#/components/common/icons';

import { BasicCheckButtonGroup, Tooltip } from '#/components/common/core/ui-kit/shadcn-ui';

defineOptions({
  name: 'PreferenceCheckboxItem',
});

withDefaults(
  defineProps<{
    disabled?: boolean;
    items: SelectOption[];
    multiple?: boolean;
    onBtnClick?: (value: string) => void;
    placeholder?: string;
  }>(),
  {
    disabled: false,
    placeholder: '',
    items: () => [],
    onBtnClick: () => {},
    multiple: false,
  },
);

const inputValue = defineModel<string[]>();

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

      <Tooltip v-if="slots.tip" side="bottom">
        <template #trigger>
          <CircleHelp class="ml-1 size-3 cursor-help" />
        </template>
        <slot name="tip"></slot>
      </Tooltip>
    </span>
    <BasicCheckButtonGroup
      v-model="inputValue"
      class="h-8 w-[165px]"
      :options="items"
      :disabled="disabled"
      :multiple="multiple"
      @btn-click="onBtnClick"
    />
  </div>
</template>
