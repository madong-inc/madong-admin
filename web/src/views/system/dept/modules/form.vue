<script lang="ts" setup>

import { computed, ref } from 'vue';

import { useModal } from '#/components/core/effects/common-ui';

import { Button } from 'ant-design-vue';

import { useForm } from '#/adapter/form';
import {  SystemDeptApi } from '#/api/system/dept';
import type{SystemDeptRow } from '#/api/system/dept';

import { $t } from '#/locale';

import { useSchema } from '../data';


const emit = defineEmits(['success']);

const api =new SystemDeptApi();

const formData = ref<SystemDeptRow>();

const getTitle = computed(() => {
  return formData.value?.id
    ? $t('ui.actionTitle.edit')
    : $t('ui.actionTitle.create');
});

const [Form, formApi] = useForm({
  // layout: 'horizontal',//vertical|horizontal
  schema: useSchema(),
  showDefaultActions: false,
});

function resetForm() {
  formApi.resetForm();
  formApi.setValues(formData.value || {});
}

const [Modal, modalApi] = useModal({
  draggable:true,
  async onConfirm() {
    const { valid } = await formApi.validate();
    if (valid) {
      modalApi.lock();
      const data = await formApi.getValues();
      try {
        await (formData.value?.id
          ? api.update({id:formData.value.id,... data})
          : api.create(data));
        modalApi.close();
        emit('success');
      } finally {
        modalApi.lock(false);
      }
    }
  },
  onOpenChange(isOpen) {
    if (isOpen) {
      const data = modalApi.getData<SystemDeptRow>();
      if (data) {
        if (data.pid === 0) {
          data.pid = undefined;
        }
        formData.value = data;
        formApi.setValues(formData.value);
      }
    }
  },
});
</script>

<template>
  <Modal :title="getTitle">
    <Form class="mx-4" />
  </Modal>
</template>
