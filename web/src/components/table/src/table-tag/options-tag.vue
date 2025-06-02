<script setup lang="ts">
import { computed } from 'vue';
import { Tag } from 'ant-design-vue';
import type { OptionsTagProps } from './types';

defineOptions({ name: 'OptionsTag' });

const props = defineProps<OptionsTagProps>();

const found = computed(() =>
  props.options.find((item) => item.value === props.value),
);
</script>

<template>
  <Tag v-if="found" :color="found.color">{{ found.label }}</Tag>
  <span v-else>未知</span>
</template>