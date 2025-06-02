<script setup lang="ts">
import { computed, ref } from "vue";

import { useDrawer } from "#/components/common-ui";
import { $t } from "#/locale";
import {cloneDeep } from "#/components/common/utils";

import { useForm } from "#/adapter/form";


import { formSchamas } from "../data";
import { SystemRecycleBinApi, SystemRecycleBinRow } from "#/api/system/recycle-bin";

const emit = defineEmits<{ reload: [] }>();

const api = new SystemRecycleBinApi();

const record=ref<SystemRecycleBinRow>()


const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-2",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 80,
  },
  schema: formSchamas(),
  showDefaultActions: false,
  wrapperClass: "grid-cols-2",
});

const [Drawer, drawerApi] = useDrawer({
  async onOpenChange(isOpen) {
    if (isOpen) {
      if (isOpen) {
        formApi.resetForm();
        const { id } = drawerApi.getData<SystemRecycleBinRow>();
          record.value = await api.get(id);
          await formApi.setValues(record.value);
          //格式化json数据显示
          const jsonString = JSON.stringify(record.value.data, null, 2);
          await formApi.setFieldValue('data',jsonString)
        }
      }
    },
    footer:false
});

const title = computed(() => {
  return record.value ? record.value?.table_name : "详情";
});

</script>

<template>
  <Drawer :title="title" class="w-[700px]">
    <Form />
  </Drawer>
</template>
