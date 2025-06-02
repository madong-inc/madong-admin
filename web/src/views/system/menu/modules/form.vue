<script lang="ts" setup>
import { computed,  ref } from 'vue';
import { useDrawer } from '#/components/common-ui';
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core';
import { useForm, z } from '#/adapter/form';
import {SystemMenuApi} from '#/api/system/menu';
import type {SystemMenuRow} from '#/api/system/menu';
import { formSchamas } from '../data';
import { $t } from '#/locale';



const api= new SystemMenuApi();

const emit = defineEmits<{
  success: [];
}>();
const formData = ref<SystemMenuRow>();
const titleSuffix = ref<string>();
const isUpdate = ref(false);

const breakpoints = useBreakpoints(breakpointsTailwind);
const isHorizontal = computed(() => breakpoints.greaterOrEqual('md').value);

const [Form, formApi] = useForm({
  showDefaultActions: false,
  ...formSchamas,
});


const [Drawer, drawerApi] = useDrawer({
  onOpenChange(isOpen) {
    isUpdate.value = drawerApi.getData()?.isUpdate ?? false;
      formData.value = isOpen ? drawerApi.getData()?.record || {} : {};
    if (isOpen && isUpdate.value) {
      api.get(formData.value?.id).then((data) => {
        formApi.setValues(data);
      });
    } else {
      formApi.setValues({pid:formData.value?.id||0});
    }
  },
  onConfirm:onSubmit,
});

/**
 * 表单提交
 */
async function onSubmit() {
  const { valid } = await formApi.validate();
  if (valid) {
    drawerApi.lock();
    const data =await formApi.getValues<Omit<any, 'children' | 'id'>>();
    try {
      //@ts-ignore
      await (isUpdate.value? api.update({id:formData.value.id, ...data}): api.create(data));
      drawerApi.close();
      emit('success');
    } finally {
      drawerApi.unlock();
    }
  }
}


const getDrawerTitle = computed(() =>
  isUpdate.value
    ? $t('ui.actionTitle.edit')
    : $t('ui.actionTitle.create'),
);
</script>
<template>
  <Drawer class="w-full max-w-[700px]" :title="getDrawerTitle">
    <Form class="mx-4" :layout="isHorizontal ? 'horizontal' : 'vertical'" />
  </Drawer>
</template>
