<script setup lang="ts">
import { BasicAvatar } from '../avatar';

interface Props {
  /**
   * @zh_CN 是否收起文本
   */
  collapsed?: boolean;
  /**
   * @zh_CN Logo 跳转地址
   */
  href?: string;
  /**
   * @zh_CN Logo 图片大小
   */
  logoSize?: number;
  /**
   * @zh_CN Logo 图标
   */
  src?: string;
  /**
   * @zh_CN Logo 文本
   */
  text: string;
  /**
   * @zh_CN Logo 主题
   */
  theme?: string;
}

defineOptions({
  name: 'BasicLogo',
});

withDefaults(defineProps<Props>(), {
  collapsed: false,
  href: 'javascript:void 0',
  logoSize: 32,
  src: '',
  theme: 'light',
});
</script>

<template>
  <div :class="theme" class="flex h-full items-center text-lg">
    <a
        :class="$attrs.class"
        :href="href"
        class="flex h-full items-center gap-2 overflow-hidden px-3 text-lg leading-normal transition-all duration-500"
    >
      <BasicAvatar
          v-if="src"
          :alt="text"
          :src="src"
          :size="logoSize"
          class="relative rounded-none bg-transparent"
      />
      <template v-if="!collapsed">
        <slot name="text">
          <span class="text-foreground truncate text-nowrap font-semibold">
            {{ text }}
          </span>
        </slot>
      </template>
    </a>
  </div>
</template>
