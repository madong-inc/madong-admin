<script setup lang="ts">
import type { ButtonType } from 'ant-design-vue/es/button';

import type { ActionItem, PopConfirm } from './types';

import { computed, type PropType, toRaw } from 'vue';

import { useAccess } from '#/components/common/effects/access';
import { isBoolean, isFunction } from '#/components/common/utils';

import { MoreOutlined } from '@ant-design/icons-vue';
import { Button, Dropdown, Menu, Popconfirm, Space } from 'ant-design-vue';

import { Icon } from '#/components/icon';

const props = defineProps({
  actions: {
    type: Array as PropType<ActionItem[]>,
    default() {
      return [];
    },
  },
  dropDownActions: {
    type: Array as PropType<ActionItem[]>,
    default() {
      return [];
    },
  },
  divider: {
    type: Boolean,
    default: true,
  },
});

const MenuItem = Menu.Item;

const { hasAccessByCodes } = useAccess();
function isIfShow(action: ActionItem): boolean {
  const ifShow = action.show;

  let isIfShow = true;

  if (isBoolean(ifShow)) {
    isIfShow = ifShow;
  }
  if (isFunction(ifShow)) {
    isIfShow = ifShow(action);
  }
  return isIfShow;
}


function isDisabled(action: ActionItem): boolean {
  const disabled = action.disabled; 
  let isDisabled = false;
 
  if (isBoolean(disabled)) {
    isDisabled = disabled;
  }
  if (isFunction(disabled)) {
    isDisabled = disabled();
  }
  return isDisabled;
}



// 禁用状态判断逻辑升级 
const isConfirmDisabled = (action: ActionItem): boolean => {
  const popConfirmDisabled = action.popConfirm?.disabled; 
  if (typeof popConfirmDisabled !== 'undefined') {
    return isDynamicBoolean(popConfirmDisabled);
  }
  return isDisabled(action); // 继承父级禁用状态 
};
 

// 动态布尔判断 
const isDynamicBoolean = (val: boolean | Function) => {
  return typeof val === 'function' ? val() : val;
}


const getActions = computed(() => {
  return (toRaw(props.actions) || [])
    .filter((action) => {
      return (
        (hasAccessByCodes(action.auth || []) ||
          (action.auth || []).length === 0) &&
        isIfShow(action)
      );
    })
    .map((action) => {
      const { popConfirm } = action;
      return {
        // getPopupContainer: document.body,
        type: 'link' as ButtonType,
        ...action,
        ...popConfirm,
        onConfirm: popConfirm?.confirm,
        onCancel: popConfirm?.cancel,
        enable: !!popConfirm,
      };
    });
});

const getDropdownList = computed((): any[] => {
  return (toRaw(props.dropDownActions) || [])
    .filter((action) => {
      return (
        (hasAccessByCodes(action.auth || []) ||
          (action.auth || []).length === 0) &&
        isIfShow(action)
      );
    })
    .map((action, index) => {
      const { label, popConfirm } = action;
      return {
        ...action,
        ...popConfirm,
        onConfirm: popConfirm?.confirm,
        onCancel: popConfirm?.cancel,
        text: label,
        divider:
          index < props.dropDownActions.length - 1 ? props.divider : false,
      };
    });
});
const getPopConfirmProps = (attrs: PopConfirm, action: ActionItem) => {
  const originAttrs: any = attrs;
  delete originAttrs.icon;
  delete originAttrs.disabled;  // 特殊处理禁用属性
  // 增加禁用状态注入
  originAttrs.disabled  = isConfirmDisabled(action);
  if (attrs.confirm && isFunction(attrs.confirm)) {
    originAttrs.onConfirm = attrs.confirm;
    delete originAttrs.confirm;
  }
  if (attrs.cancel && isFunction(attrs.cancel)) {
    originAttrs.onCancel = attrs.cancel;
    delete originAttrs.cancel;
  }
  return originAttrs;
};
const getButtonProps = (action: ActionItem) => {
  const res = {
    type: action.type || 'primary',
    disabled: isDisabled(action), // 新增 disabled 状态绑定 
    ...action,
  };
  delete res.icon;
  return res;
};
const handleMenuClick = (e: any) => {
  const action = getDropdownList.value[e.key];
  if (action.onClick && isFunction(action.onClick)) {
    action.onClick();
  }
};
</script>

<template>
  <div class="m-table-action">
    <Space>
      <template v-for="(action, index) in getActions" :key="index">
        <Popconfirm
          v-if="action.popConfirm"
          :disabled="isConfirmDisabled(action)"
          v-bind="getPopConfirmProps(action.popConfirm,action)"
        >
          <template v-if="action.popConfirm.icon" #icon>
            <Icon :icon="action.popConfirm.icon" />
          </template>
          <Button 
          v-bind="getButtonProps(action)"          
          :disabled="isDisabled(action)"
          @click="!isDisabled(action) && action.onClick?.()" 
          >
            <Icon v-if="action.icon" :icon="action.icon" />
            {{ action.label }}
          </Button>
        </Popconfirm>
        <Button
         v-else 
         v-bind="getButtonProps(action)"
         :disabled="isDisabled(action)"
          >
          <Icon v-if="action.icon" :icon="action.icon" />
          {{ action.label }}
        </Button>
      </template>
    </Space>

    <Dropdown v-if="getDropdownList.length > 0" :trigger="['hover']">
      <slot name="more">
        <Button size="small" type="link">
          <MoreOutlined class="icon-more" />
        </Button>
      </slot>
      <template #overlay>
        <Menu @click="handleMenuClick">
          <MenuItem v-for="(action, index) in getDropdownList" :key="index">
            <template v-if="action.popConfirm">
              <Popconfirm v-bind="getPopConfirmProps(action.popConfirm)">
                <template v-if="action.popConfirm.icon" #icon>
                  <Icon :icon="action.popConfirm.icon" />
                </template>
                <div>
                  <Icon v-if="action.icon" :icon="action.icon" />
                  <span class="ml-1">{{ action.text }}</span>
                </div>
              </Popconfirm>
            </template>
            <template v-else>
              <Icon v-if="action.icon" :icon="action.icon" />
              {{ action.label }}
            </template>
          </MenuItem>
        </Menu>
      </template>
    </Dropdown>
  </div>
</template>
