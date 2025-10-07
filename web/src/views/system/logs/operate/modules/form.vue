<script setup lang="ts">
import { computed, ref } from "vue";

import { useDrawer } from "#/components/core/effects/common-ui";
import { $t } from "#/locale";
import {cloneDeep } from "#/components/core/utils";

import { useForm } from "#/adapter/form";


import { formSchamas } from "../data";
import { SystemOperateLogApi,type SystemOperateRecordRow } from "#/api/system/logs";


const emit = defineEmits<{ reload: [] }>();

const api = new SystemOperateLogApi();

const record=ref<SystemOperateRecordRow>()


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
        const { id } = drawerApi.getData<SystemOperateRecordRow>();
          record.value = await api.get(id);
          await formApi.setValues(record.value);
          //格式化json数据显示
          const paramString = JSON.stringify(record.value.param, null, 2);
          const resultString = JSON.stringify(record.value.result, null, 2);
          await formApi.setFieldValue('param',paramString)
          await formApi.setFieldValue('result',resultString)
        }
      }
    },
    footer:false
});

const title = computed(() => {
  return $t('system.operate_logs.form.detail');
});

</script>

<template>
  <Drawer :title="title" class="w-[700px]">
    <Form />
  </Drawer>
</template>
