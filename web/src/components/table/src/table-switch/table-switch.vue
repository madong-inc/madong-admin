<script setup lang="ts">
import { computed, ref } from 'vue';

import { $t } from '#/locale';

import { Modal, Switch } from 'ant-design-vue';
import { isFunction } from '#/components/common/utils';
import type { CheckedType,Props} from './types';


const props = withDefaults(defineProps<Props>(), {
  checkedText: undefined,
  unCheckedText: undefined,
  checkedValue: '0',
  unCheckedValue: '1',
  confirm: false,
  confirmText: undefined,
});

const emit = defineEmits<{ reload: [] }>();

// 修改为computed 支持语言切换
const checkedTextComputed = computed(() => {
  return props.checkedText ?? $t('pages.common.enabled');
});

const unCheckedTextComputed = computed(() => {
  return props.unCheckedText ?? $t('pages.common.disabled');
});

const currentChecked = defineModel<CheckedType>('value', {
  default: false,
});

const loading = ref(false);

function confirmUpdate(checked: CheckedType, lastStatus: CheckedType) {
  const content = isFunction(props.confirmText)
    ? props.confirmText(checked)
    : `确认要更新状态吗？`;

  Modal.confirm({
    title: '提示',
    content,
    centered: true,
    onOk: async () => {
      try {
        loading.value = true;
        const { api } = props;
        isFunction(api) && (await api());
        emit('reload');
      } catch {
        currentChecked.value = lastStatus;
      } finally {
        loading.value = false;
      }
    },
    onCancel: () => {
      currentChecked.value = lastStatus;
    },
  });
}

async function handleChange(checked: CheckedType, e: Event) {
  // 阻止事件冒泡 否则会跟行选中冲突
  e.stopPropagation();
  const { checkedValue, unCheckedValue } = props;
  // 原本的状态
  const lastStatus = checked === checkedValue ? unCheckedValue : checkedValue;
  // 切换状态
  currentChecked.value = checked;
  const { api } = props;
  try {
    loading.value = true;

    if (props.confirm) {
      confirmUpdate(checked, lastStatus);
      return;
    }

    isFunction(api) && (await api());
    emit('reload');
  } catch {
    currentChecked.value = lastStatus;
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <Switch
    v-bind="$attrs"
    :loading="loading"
    :disabled="disabled"
    :checked="currentChecked"
    :checked-children="checkedTextComputed"
    :checked-value="checkedValue"
    :un-checked-children="unCheckedTextComputed"
    :un-checked-value="unCheckedValue"
    @change="handleChange"
  />
</template>
