<script lang="ts" setup>
import type { DataNode } from "ant-design-vue/es/tree";

import type { Recordable } from "#/components/core/types";

import { computed, ref } from "vue";

import { useDrawer, Tree, useModal } from "#/components/core/effects/common-ui";
import { IconifyIcon } from "#/components/core/icons";

import { Spin } from "ant-design-vue";

import { useForm } from "#/adapter/form";
import { SystemDeptApi } from "#/api/system/dept";
import { SystemRoleApi } from "#/api/system/role";
import { SystemAuthApi } from "#/api/system/auth";
import { $t } from "#/locale";

import { dataScopeFormSchema } from "../data";
import type{ SystemRoleRow } from "#/api/system/role/role-model";

const api = new SystemRoleApi();
const deptApi = new SystemDeptApi();
const authApi = new SystemAuthApi();

const emits = defineEmits(["success"]);

const formData = ref<SystemRoleRow>();

const [Form, formApi] = useForm({
  schema: dataScopeFormSchema(),
  showDefaultActions: false,
});

const permissions = ref<DataNode[]>([]);
const loadingPermissions = ref(false);

const id = ref();
const [Modal, modalApi] = useModal({
  draggable:true,
  async onConfirm() {
    const { valid } = await formApi.validate();
    if (!valid) return;
    const values = await formApi.getValues();
    modalApi.lock();
    api.dataScope({...values}).then(() => {
        emits("success");
        modalApi.close();
      })
      .catch(() => {
        modalApi.unlock();
      });
  },
  async onOpenChange(isOpen) {
    if (isOpen) {
      const data = modalApi.getData<SystemRoleRow>();
      formApi.resetForm();
      if (isOpen) {
        formData.value = data.record;
        id.value = data.record?.id;
        /**
         * 角色自定义权限id 获取
         */
        let result = await authApi.roleScopeIds({
          role_id: data.record.id,
        });
        //@ts-ignore
        formData.value.permissions=result;
        formApi.setValues(formData.value);
      } else {
        id.value = undefined;
      }

      if (permissions.value.length === 0) {
        loadPermissions();
      }
    }
  },
});

async function loadPermissions() {
  loadingPermissions.value = true;
  try {
    const res = await deptApi.list({ format: "table_tree", page: 1, limit: 9999 });
    permissions.value = (res as unknown) as DataNode[];
  } finally {
    loadingPermissions.value = false;
  }
}

const getTitle = $t('system.role.form.data_scope.title');


</script>
<template>
  <Modal :title="getTitle" class="w-full max-w-[700px]">
    <Form>
      <template #permissions="slotProps">
        <Spin :spinning="loadingPermissions" wrapper-class-name="w-full">
          <Tree
            :tree-data="permissions"
            multiple
            bordered
            :default-expanded-level="2"
            v-bind="slotProps"
            value-field="id"
            label-field="name"
            icon-field="icon"
          >
            <template #node="{ value }">
              <IconifyIcon v-if="value?.icon" :icon="value.icon" />
              {{ $t(value.name) }}
            </template>
          </Tree>
        </Spin>
      </template>
    </Form>
  </Modal>
</template>
<style lang="css" scoped>
:deep(.ant-tree-title) {
  .tree-actions {
    display: none;
    margin-left: 20px;
  }
}

:deep(.ant-tree-title:hover) {
  .tree-actions {
    display: flex;
    flex: auto;
    justify-content: flex-end;
    margin-left: 20px;
  }
}
</style>
