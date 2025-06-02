<script setup lang="ts">
import { computed, h } from 'vue';

import { IconifyIcon as BasicIcon } from '#/components/common/icons';
import { capitalizeFirstLetter, kebabToCamelCase } from '#/components/common/utils';

import * as icons from '@ant-design/icons-vue';

const props = defineProps({
  icon: {
    type: String,
    default: '',
  },
  size: {
    type: [String, Number],
    default: '16px',
  },
  class:{
    type:[String,Array],
    default:'',
  }
});

const baseClass = 'm-icon__'; // 定义基础类名

const iconComp = computed(() => { 
  if (props.icon.startsWith('ant-design:'))  { 
    const icon = capitalizeFirstLetter( 
      kebabToCamelCase(props.icon.replace('ant-design:',  '')), 
    );
    return (icons as any)[icon];
  } else if (props.icon.startsWith('http'))  {
    return () => h('img', { src: props.icon  }); 
  }
  return '';
});

const styles = computed(() => {
  return {
    fontSize: props.size.toString().endsWith('px')
      ? props.size
      : `${props.size}px`,
  };
});
</script>

<template>
 <component 
    :is="iconComp" 
    v-if="iconComp" 
    :style="styles" 
    :class="[baseClass, props.class]"  
  /> 
  <BasicIcon v-else :icon="props.icon" :style="styles"  :class="[baseClass, props.class]" class="m-icon__" />
</template>
<style lang="less" scoped>
.m-icon__ {
  display: inline-flex;
  align-items: center;
  width: 1em;
  height: 1em;
  font-style: normal;
  line-height: 0;
  color: inherit;
  text-align: center;
  text-transform: none;
  vertical-align: -0.125em;
  text-rendering: optimizelegibility;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
</style>

