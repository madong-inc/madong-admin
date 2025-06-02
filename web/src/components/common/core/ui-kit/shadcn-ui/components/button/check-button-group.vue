<script lang="ts" setup>
import type { Arrayable } from '@vueuse/core';

import type { ValueType, BasicButtonGroupProps } from './button';

import { computed, ref, watch } from 'vue';

import { Circle, CircleCheckBig, LoaderCircle } from '#/components/common/icons';
import { BasicRenderContent } from '../render-content';
import { cn, isFunction } from '#/components/common/utils';

import { objectOmit } from '@vueuse/core';

import BasicButtonGroup from './button-group.vue';
import Button from './button.vue';

const props = withDefaults(defineProps<BasicButtonGroupProps>(), {
  gap: 0,
  multiple: false,
  showIcon: true,
  size: 'middle',
});
const emit = defineEmits(['btnClick']);
const btnDefaultProps = computed(() => {
  return {
    ...objectOmit(props, ['options', 'btnClass', 'size', 'disabled']),
    class: cn(props.btnClass),
  };
});
const modelValue = defineModel<Arrayable<ValueType> | undefined>();

const innerValue = ref<Array<ValueType>>([]);
const loadingValues = ref<Array<ValueType>>([]);
watch(
  () => props.multiple,
  (val) => {
    if (val) {
      modelValue.value = innerValue.value;
    } else {
      modelValue.value =
        innerValue.value.length > 0 ? innerValue.value[0] : undefined;
    }
  },
  { immediate: true },
);

watch(
  () => modelValue.value,
  (val) => {
    if (Array.isArray(val)) {
      const arrVal = val.filter((v) => v !== undefined);
      if (arrVal.length > 0) {
        innerValue.value = props.multiple
          ? [...arrVal]
          : [arrVal[0] as ValueType];
      } else {
        innerValue.value = [];
      }
    } else {
      innerValue.value = val === undefined ? [] : [val as ValueType];
    }
  },
  { deep: true },
);

async function onBtnClick(value: ValueType) {
  if (props.beforeChange && isFunction(props.beforeChange)) {
    try {
      loadingValues.value.push(value);
      const canChange = await props.beforeChange(
        value,
        !innerValue.value.includes(value),
      );
      if (canChange === false) {
        return;
      }
    } finally {
      loadingValues.value.splice(loadingValues.value.indexOf(value), 1);
    }
  }

  if (props.multiple) {
    if (innerValue.value.includes(value)) {
      innerValue.value = innerValue.value.filter((item) => item !== value);
    } else {
      innerValue.value.push(value);
    }
    modelValue.value = innerValue.value;
  } else {
    innerValue.value = [value];
    modelValue.value = value;
  }
  emit('btnClick', value);
}
</script>
<template>
  <BasicButtonGroup
    :size="props.size"
    :gap="props.gap"
    class="basic-check-button-group"
  >
    <Button
      v-for="(btn, index) in props.options"
      :key="index"
      :class="cn('border', props.btnClass)"
      :disabled="
        props.disabled ||
        loadingValues.includes(btn.value) ||
        (!props.multiple && loadingValues.length > 0)
      "
      v-bind="btnDefaultProps"
      :variant="innerValue.includes(btn.value) ? 'default' : 'outline'"
      @click="onBtnClick(btn.value)"
    >
      <div class="icon-wrapper" v-if="props.showIcon">
        <LoaderCircle
          class="animate-spin"
          v-if="loadingValues.includes(btn.value)"
        />
        <CircleCheckBig v-else-if="innerValue.includes(btn.value)" />
        <Circle v-else />
      </div>
      <slot name="option" :label="btn.label" :value="btn.value">
        <BasicRenderContent :content="btn.label" />
      </slot>
    </Button>
  </BasicButtonGroup>
</template>
<style lang="scss" scoped>
.basic-check-button-group {
  &:deep(.size-large) button {
    .icon-wrapper {
      margin-right: 0.3rem;

      svg {
        width: 1rem;
        height: 1rem;
      }
    }
  }

  &:deep(.size-middle) button {
    .icon-wrapper {
      margin-right: 0.2rem;

      svg {
        width: 0.75rem;
        height: 0.75rem;
      }
    }
  }

  &:deep(.size-small) button {
    .icon-wrapper {
      margin-right: 0.1rem;

      svg {
        width: 0.65rem;
        height: 0.65rem;
      }
    }
  }
}
</style>
