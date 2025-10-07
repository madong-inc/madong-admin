<script lang="ts" setup>
import { ref } from 'vue';

import { useModal } from '#/components/core/effects/common-ui';

import { message } from 'ant-design-vue';
import { useForm } from '#/adapter';
import { SystemDictApi } from '#/api/system/dict';
import type {  SystemDictRow } from '#/api/system/dict';


import { formSchamas } from '../data';
import { $t } from '#/locale';

const api= new SystemDictApi();
const emits = defineEmits(["success"]);

const formData = ref<SystemDictRow>();
const id = ref();

const [Form, formApi] = useForm({
  schema: formSchamas(),
  showDefaultActions: false,
});


const [Modal, modalApi] = useModal({
  async onConfirm() {
    const { valid } = await formApi.validate();
    if (!valid) return;
    const values = await formApi.getValues();
    modalApi.lock();
    //@ts-ignore
    (id.value ? api.update({...values,id:id.value}) : api.create(values))
      .then(() => {
        emits("success");
        modalApi.close();
      })
      .catch(() => {
        modalApi.unlock();
      });
  },
  async onOpenChange(isOpen) {
    if (isOpen) {
      const data = modalApi.getData<SystemDictRow>();
      formApi.resetForm();
      id.value= data.id||undefined;
      if (isOpen && id.value) {
        const result= await api.get(id.value);
        formApi.setValues(result);
      } else {
        id.value = undefined;
      }
    }
  },
});

function getTitle():string{
 return  id.value
    ? $t("common.edit")
    : $t("common.create");
}
</script>
<template>
  <Modal :title="getTitle()" class="w-[50%]">
      <Form />
  </Modal>
</template>
