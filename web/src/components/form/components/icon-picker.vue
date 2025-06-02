<script setup lang="ts">
import { computed, ref } from 'vue';

import * as icons from '@ant-design/icons-vue';
import { useVModel } from '@vueuse/core';
import { Input, Pagination, Popover } from 'ant-design-vue';

import { Icon } from '#/components/icon';
import { toKebabCase } from '#/utils';

const props = defineProps({
  value: {
    type: [String],
    default: undefined,
  },
  allowClear: {
    type: Boolean,
    default: true,
  },
  readonly: {
    type: Boolean,
    default: true,
  },
});
const emits = defineEmits(['update:value']);
const visible = ref(false);
const mValue = useVModel(props, 'value', emits, {
  defaultValue: props.value,
  passive: true,
});
const pageSize = ref(120);
const pageNum = ref(1);
const searchValue = ref('');
const trigger = ref<HTMLDivElement>();

const triggerPopover = () => {
  if (trigger.value) {
    trigger.value.click();
  }
};
const getIconKeys = computed(() => {
  return Object.keys(icons).filter((key) => {
    // 移除双色的图标
    return (
      !key.endsWith('TwoTone') &&
      ![
        'createFromIconfontCN',
        'default',
        'getTwoToneColor',
        'setTwoToneColor',
      ].includes(key)
    );
  });
});
const iconData = computed(() => {
  const keys = getIconKeys.value.slice(
    (pageNum.value - 1) * pageSize.value,
    pageNum.value * pageSize.value,
  );
  return keys
    .map((item) => {
      return {
        icon: `ant-design:${toKebabCase(`${item}`)}`,
      };
    })
    .filter((item) => {
      if (searchValue.value) {
        return item.icon.includes(searchValue.value);
      }
      return true;
    });
});
const getTotal = computed(() => {
  return searchValue.value
    ? iconData.value.length
    : Object.keys(getIconKeys.value).length;
});
const handlePageChange = (page: number) => {
  pageNum.value = page;
};
</script>
<template>
  <Input
    v-model:value="mValue"
    :allow-clear="allowClear"
    :readonly="readonly"
    @click="triggerPopover"
  >
    <template #addonAfter>
      <Popover v-model="visible" placement="bottomLeft" trigger="click">
        <template #title>
          <div class="flex justify-between">
            <Input
              v-model:value="searchValue"
              allow-clear
              placeholder="搜索图标"
            />
          </div>
        </template>

        <template #content>
          <div class="grid grid-cols-12" style="height: 200px; overflow: auto">
            <div
              v-for="item in iconData"
              :key="item.icon"
              :class="
                item.icon === mValue
                  ? 'm-icon-wrap-selected col-span-2 flex justify-center'
                  : 'm-icon-wrap col-span-2 flex justify-center'
              "
              @click="mValue = item.icon"
            >
              <Icon :icon="item.icon" />
            </div>
          </div>
          <div
            v-if="getTotal >= pageSize"
            class="flex items-center justify-center py-2"
          >
            <Pagination
              :page-size="pageSize"
              :show-size-changer="false"
              :total="getTotal"
              show-less-items
              size="small"
              @change="handlePageChange"
            />
          </div>
        </template>
        <div ref="trigger">
          <Icon :icon="mValue || 'ant-design:table-outlined'" />
        </div>
      </Popover>
    </template>
  </Input>
</template>
<style lang="less" scoped>
.m-icon-wrap {
  height: 36px;
  cursor: pointer;
}
.m-icon-wrap:hover {
  border: 1px solid blue;
}
.m-icon-wrap-selected {
  height: 36px;
  border: 1px solid blue;
  cursor: pointer;
}
</style>
