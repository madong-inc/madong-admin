<script setup lang="ts">
import { computed, ref } from "vue";

import { useDrawer } from "#/components/common-ui";
import { $t } from "#/locale";
import {cloneDeep } from "#/components/common/utils";

import { useForm } from "#/adapter/form";


import { formSchamas } from "../data";
import { SystemDataSourceApi,type SystemDataSourceRow} from "#/api/system/data-source";


const emit = defineEmits<{ reload: [] }>();

const api = new SystemDataSourceApi();

const record=ref<SystemDataSourceRow>()
const isUpdate= ref<Boolean>(false);


const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-2",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 110,
  },
  schema: formSchamas(),
  showDefaultActions: false,
  wrapperClass: "grid-cols-2",
});

const [Drawer, drawerApi] = useDrawer({
  async onOpenChange(isOpen) {
    if (isOpen) {
        formApi.resetForm();
        const { id } = drawerApi.getData<SystemDataSourceRow>();
        //编辑
        if(id){
          isUpdate.value=true;
          record.value = await api.get(id);
          await formApi.setValues(record.value);
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
  return record.value ? '编辑': "新增";
});

</script>

<template>
  <Drawer :title="title" class="w-[700px]">
    <Form />
  </Drawer>
</template>
