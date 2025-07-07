<script setup lang="ts">
import { computed, reactive, ref } from "vue";

import { useModal } from "#/components/common-ui";
import { $t } from "#/locale";
import { cloneDeep } from "#/components/common/utils";

import { useForm } from "#/adapter/form";

import { omit } from "lodash-es";

import { ProcessCategoryApi } from "#/api/wf/category";
import type { ProcessCategoryRow } from "#/api/wf/category";
import { SystemUserApi } from "#/api/system/user";
import { getDictOptions } from "#/utils";
import { SystemAuthApi } from "#/api/system/auth";

const emit = defineEmits<{ reload: [] }>();

const api = new SystemAuthApi();

const record = ref<any>();
const tenant = ref([]);

const [Form, formApi] = useForm({
  commonConfig: {
    formItemClass: "col-span-3",
    componentProps: {
      class: "w-full",
    },
    labelWidth: 110,
  },
  schema: [
    {
      fieldName: "tenant_id",
      label: "租户名称",
      component: "Select",
      componentProps: {
        placeholder: "请选择租户",
        allowClear: true,
        options: tenant,
      },
      formItemClass: "col-span-2",
      rules: "selectRequired",
    },
  ],
  showDefaultActions: false,
  wrapperClass: "grid-cols-3 mt-10",
});

const state = reactive([]);

const [Modal, modalApi] = useModal({
  draggable: true,
  fullscreenButton: false,
  async onOpenChange(isOpen) {
    tenant.value = [];
    if (isOpen) {
      record.value = await api.getUserTenant();
      tenant.value = generateOptions(record?.value?.managed_tenants || []);
      formApi.setFieldValue("tenant_id", record?.value["current_tenant_id"]);
    }
  },
  onConfirm: handleConfirm,
});

/**
 * 租户列表
 * @param data
 */
function generateOptions(data) {
  return data.map((item) => {
    return {
      label: item.company_name,
      value: item.id,
    };
  });
}

/**
 * 表单提交
 */
async function handleConfirm() {
  try {
    modalApi.lock(true);
    const { valid } = await formApi.validate();
    if (!valid) {
      return;
    }
    const data = cloneDeep(await formApi.getValues());
    if(record.value.current_tenant_id == data['tenant_id']){
        return;
    }
    console.log(data);
    await api.changeTenant(data);
    modalApi.close();
    emit('reload');
  } catch (error) {
    console.error(error);
  } finally {
    modalApi.lock(false);
  }
}

const title = computed(() => {
  return "切换租户";
});
</script>

<template>
  <Modal :title="title" class="w-[700px]">
    <Form></Form>
  </Modal>
</template>
