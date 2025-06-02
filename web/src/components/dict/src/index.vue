<!-- eslint-disable -->
<script setup lang="ts">
import type { DictItem } from '#/api/system/dict/dict-item-model';

import { computed } from 'vue';

import { Spin, Tag } from 'ant-design-vue';

import { tagTypes } from './data';

interface Props {
  dicts: DictItem[]; 
  value: number | string;
}

const props = withDefaults(defineProps<Props>(), {
  dicts: undefined,
});

const color = computed<string>(() => {
  const current = props.dicts.find((item) => item.value == props.value);
  const style = current?.color ?? '';
  // 是否为默认的颜色
  const isDefault = Reflect.has(tagTypes, style);
  // 判断是默认还是自定义颜色
  if (isDefault) {
    // 这里做了antd - element-plus的兼容
    return tagTypes[style]!.color;
  }
  return style;
});

const otherClass = computed<string>(() => {
  const current = props.dicts.find((item) => item.value == props.value);
  return current?.other_class ?? '';
});

const label = computed<number | string>(() => {
  const current = props.dicts.find((item) => item.value == props.value);
  return current?.label ?? 'unknown';
});

const tagComponent = computed(() => (color.value ? Tag : 'div'));

const loading = computed(() => {
  return props.dicts?.length === 0;
});
</script>

<template>
  <div>
    <component
      v-if="!loading"
      :is="tagComponent"
      :class="otherClass"
      :color="color"
    >
      {{ label }}
    </component>
    <Spin v-else :spinning="true" size="small" />
  </div>
</template>
