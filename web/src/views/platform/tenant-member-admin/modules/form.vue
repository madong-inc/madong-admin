<script lang="ts" setup>
import { computed, ref } from "vue";

import { useModal } from "#/components/common-ui";

import { message } from "ant-design-vue";

import { useForm } from "#/adapter";

import { formSchamas } from "../data";
import { $t } from "#/locale";
import { getPopupContainer } from "#/utils";
import { TenantMemberAdminApi } from "#/api/platform/tenant-member";
import type { TenantMemberAdminRow } from "#/api/platform/tenant-member";

const emit = defineEmits<{ success: [] }>();

const api = new TenantMemberAdminApi();

const record = ref();
const isUpdate = ref(false);

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

const [Modal, modalApi] = useModal({
  draggable: true,
  async onOpenChange(isOpen) {
    if (!isOpen) {
      formApi.updateSchema([
        {
          componentProps: {
            options: [],
            placeholder: $t(
              "platform.tenant_member_admin.form.modal.placeholder.tenant_id"
            ),
          },
          fieldName: "tenant_id",
        },
      ]);
      return null;
    }

    isUpdate.value = false;
    const result = modalApi.getData();
    formApi.setFieldValue("admin_id", result.admin_id);
    isUpdate.value = result?.is_update || false;

    // 租户选择、相关操作并行处理
    await fetchTenantSelect();
    if (isUpdate.value) {
      const info = await api.get(result.id); //获取用户租户信息详情
      formApi.setValues(info);
    }
  },
  onConfirm() {
    formApi.validate().then(async (e: any) => {
      if (e.valid) {
        const values = (await formApi.getValues()) as TenantMemberAdminRow;
        modalApi.setState({ loading: true, confirmLoading: true });
        if (isUpdate?.value) {
          api
            .update(values)
            .then(() => {
              message.success($t("platform.tenant_member_admin.message.success"));
              emit("success");
              modalApi.close();
            })
            .finally(() => {
              modalApi.setState({ loading: false, confirmLoading: false });
            });
        } else {
          api
            .create(values)
            .then(() => {
              message.success($t("platform.tenant_member_admin.message.success"));
              emit("success");
              modalApi.close();
            })
            .finally(() => {
              modalApi.setState({ loading: false, confirmLoading: false });
            });
        }
      }
    });
  },
});

/**
 * 租户列表处理
 */
async function fetchTenantSelect() {
  const options = await api.getTenant({ format: "select" });
  formApi.updateSchema([
    {
      componentProps: (formModel) => ({
        class: "w-full",
        fieldNames: {
          key: "label",
          value: "value",
          label: "label",
          children: "children",
        },
        getPopupContainer,
        async onSelect(tenantId: number | string) {
          //可以做联动
        },
        placeholder: $t("platform.tenant_member_admin.form.modal.placeholder.tenant_id"),
        showSearch: true,
        treeData: options,
        treeDefaultExpandAll: true,
        treeLine: { showLeafIcon: false },
        // 筛选的字段
        treeNodeFilterProp: "label",
        // 选中后显示在输入框的值
        treeNodeLabelProp: "label",
      }),
      fieldName: "tenant_id",
    },
  ]);
}

const title = computed(() => {
  return isUpdate.value ? $t("common.edit") : $t("common.create");
});
</script>
<template>
  <Modal :title="title" class="w-[40%]">
    <div class="common-form">
      <Form></Form>
    </div>
  </Modal>
</template>
