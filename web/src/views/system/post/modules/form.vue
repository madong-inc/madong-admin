<script setup lang="ts">
import { computed, ref } from "vue";

import { useDrawer } from "#/components/common-ui";
import { $t } from "#/locale";
import {cloneDeep } from "#/components/common/utils";

import { useForm } from "#/adapter/form";
import {  SystemPostApi } from "#/api/system/post";
import type{  SystemPostRow } from "#/api/system/post";


import { drawerSchema } from "../data";

const emit = defineEmits<{ reload: [] }>();

const api = new SystemPostApi();

const isUpdate = ref(false);
const title = computed(() => {
  return isUpdate.value ? $t("common.edit") : $t("common.create");
});

const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-2",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 80,
  },
  schema: drawerSchema(),
  showDefaultActions: false,
  wrapperClass: "grid-cols-2",
});

const [Drawer, drawerApi] = useDrawer({
  onClosed: handleClosed,
  onConfirm: handleConfirm,
  async onOpenChange(isOpen) {
    if (isOpen) {
      if (isOpen) {
        formApi.resetForm();
        const { id } = drawerApi.getData<SystemPostRow>();
        isUpdate.value = false;
        if (id !== undefined) {
          isUpdate.value = true;
          const record = await api.get(id);
          await formApi.setValues(record);
        }
      }
    }
  },
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

async function handleClosed() {
  await formApi.resetForm();
}
</script>

<template>
  <Drawer :title="title" class="w-[600px]">
    <Form />
  </Drawer>
</template>
