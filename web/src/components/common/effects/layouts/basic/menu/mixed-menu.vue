<script lang="ts" setup>
import type { MenuRecordRaw } from '#/components/common/core/typings';

import type { NormalMenuProps } from '#/components/common/core/ui-kit/menu-ui';

import { onBeforeMount } from 'vue';
import { useRoute } from 'vue-router';

import { findMenuByPath } from '#/components/common/utils';

import { NormalMenu } from '#/components/common/core/ui-kit/menu-ui';

interface Props extends NormalMenuProps {}

const props = defineProps<Props>();

const emit = defineEmits<{
  defaultSelect: [MenuRecordRaw, MenuRecordRaw?];
  enter: [MenuRecordRaw];
  select: [MenuRecordRaw];
}>();

const route = useRoute();

onBeforeMount(() => {
  const menu = findMenuByPath(props.menus || [], route.path);
  if (menu) {
    const rootMenu = (props.menus || []).find(
      (item) => item.path === menu.parents?.[0],
    );
    emit('defaultSelect', menu, rootMenu);
  }
});
</script>

<template>
  <NormalMenu
    :active-path="activePath"
    :collapse="collapse"
    :menus="menus"
    :rounded="rounded"
    :theme="theme"
    @enter="(menu) => emit('enter', menu)"
    @select="(menu) => emit('select', menu)"
  />
</template>
