<script setup lang="ts">
import { computed, ref } from "vue";

import { useDrawer } from "#/components/core/effects/common-ui";
import { $t } from "#/locale";
import {cloneDeep } from "#/components/core/utils";

import { useForm } from "#/adapter/form";


import { formSchamas } from "../data";
import { GatewayLimiterApi } from "#/api/dev/gateway";
import type{ GatewayLimiterRow } from "#/api/dev/gateway";



const emit = defineEmits<{ reload: [] }>();

const api = new GatewayLimiterApi();

const record=ref<GatewayLimiterRow>()
const isUpdate= ref<Boolean>(false);


const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: 'col-span-12',
    componentProps: {
      class: "w-full",
    },
    labelWidth: 100,
  },
  schema: formSchamas(),
  showDefaultActions: false,
   wrapperClass: 'grid-cols-12'
});

const [Drawer, drawerApi] = useDrawer({
  async onOpenChange(isOpen) {
    if (isOpen) {
        isUpdate.value=false;
        formApi.resetForm();
        const { id } = drawerApi.getData<GatewayLimiterRow>();
        //编辑
        if(id){
          isUpdate.value=true;
          record.value = await api.get(id);
          const { cycle_rule = {} } = record.value || {}
          await formApi.setValues({...record.value,...cycle_rule});
        }
      }
    },
    onConfirm: handleConfirm,
});


/**
 * 表单提交
 */
 async function handleConfirm() {
  try {
    drawerApi.lock(true);
    const { valid } = await formApi.validate();
    if (!valid) {
      return;
    }
    const data = cloneDeep(await formApi.getValues());
    await (isUpdate.value ? api.update(data) : api.create(data));
    emit("reload");
    drawerApi.close();
  } catch (error) {
    console.error(error);
  } finally {
    drawerApi.lock(false);
  }
}

const title = computed(() => {
  return isUpdate.value ? $t('common.edit'): $t('common.create');
});
</script>

<template>
  <Drawer :title="title" class="w-[700px]">
    <Form />
  </Drawer>
</template>
