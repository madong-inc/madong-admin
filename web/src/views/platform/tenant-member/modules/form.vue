<script setup lang="ts">
import { computed, h, ref } from "vue";

import { useDrawer } from "#/components/common-ui";
import { $t } from "#/locale";
import { addFullName, cloneDeep, getPopupContainer } from "#/components/common/utils";

import { useForm } from "#/adapter/form";
import { drawerSchema } from "../data";
import { defaultFormValueGetter, useBeforeCloseDiff } from "#/utils/popup";
import { Tag } from "ant-design-vue";
import { TenantMemberApi } from "#/api/platform/tenant-member";
import type { TenantMemberRow } from "#/api/platform/tenant-member";


const emit = defineEmits<{ reload: [] }>();

const api = new TenantMemberApi();

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
    const { id } = drawerApi.getData() as { id?: number | string };
    isUpdate.value = !!id;
    if (isOpen) {
      /** update时 禁用用户名修改 不显示密码框 */
      formApi.updateSchema([
        { componentProps: { disabled: isUpdate.value }, fieldName: "user_name" },
        {
          dependencies: { show: () => !isUpdate.value, triggerFields: ["id"] },
          fieldName: "password",
        },
      ]);
     const data=  await api.get(id);
      formApi.setValues(data)
    }
  },
});

/**
 * 默认密码
 */
const defaultPassword = ref("");
async function loadDefaultPassword(update: boolean) {
  if (!update && defaultPassword.value) {
    formApi.setFieldValue("password", defaultPassword.value);
  }
}

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
    if (!isUpdate.value) {
      delete data.id;
    }
    
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
  <Drawer :title="title" class="w-[50%]">
    <Form />
  </Drawer>
</template>
