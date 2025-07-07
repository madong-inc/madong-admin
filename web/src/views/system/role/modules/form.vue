<script lang="ts" setup>
import type { DataNode } from "ant-design-vue/es/tree";

import type { Recordable } from "#/components/common/types";

import { computed, ref } from "vue";

import { useDrawer, Tree } from "#/components/common-ui";
import { IconifyIcon } from "#/components/common/icons";

import { Spin, Tag } from "ant-design-vue";

import { useForm } from "#/adapter/form";
import { SystemRoleApi } from "#/api/system/role";
import { SystemAuthApi } from "#/api/system/auth";
import { $t } from "#/locale";

import { formSchemas } from "../data";
import type { SystemRoleRow } from "#/api/system/role";

const api = new SystemRoleApi();
const authApi = new SystemAuthApi();

const emits = defineEmits(["success"]);

const formData = ref<SystemRoleRow>();

const [Form, formApi] = useForm({
  schema: formSchemas(),
  showDefaultActions: false,
});

const permissions = ref<DataNode[]>([]);
const loadingPermissions = ref(false);

const id = ref();
const [Drawer, drawerApi] = useDrawer({
  async onConfirm() {
    const { valid } = await formApi.validate();
    if (!valid) return;
    const values = await formApi.getValues();
    drawerApi.lock();

    const filteredData = {
      ...values,
      permissions: filterPermissions(values?.permissions||[]),
    };

    (id.value ? api.update({ ...filteredData, id: id.value }) : api.create(filteredData))
      .then(() => {
        emits("success");
        drawerApi.close();
      })
      .catch(() => {
        drawerApi.unlock();
      });
  },
  async onOpenChange(isOpen) {
    if (isOpen) {
      const data = drawerApi.getData<SystemRoleRow>();
      formApi.resetForm();
      if (isOpen && data.isUpdate) {
        formData.value = data.record;
        id.value = data.record?.id;
        let result = await authApi.roleMenuIds({
          role_id: data.record.id,
        });
        //@ts-ignore
        formData.value.permissions = result;
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

function filterPermissions(permissions: (string | null | undefined)[]): string[] {
  return permissions.filter((permission): permission is string => permission != null);
}

async function loadPermissions() {
  loadingPermissions.value = true;
  try {
    const res = await authApi.getUserPermission();
    permissions.value = (res as unknown) as DataNode[];
  } finally {
    loadingPermissions.value = false;
  }
}

const getDrawerTitle = computed(() => {
  return formData.value?.id ? $t("common.edit") : $t("common.create");
});

function getNodeClass(node: Recordable<any>) {
  const classes: string[] = [];
  if (node.value?.type === 3) {
    classes.push("inline-flex");
    if (node.index % 3 >= 1) {
      classes.push("!pl-0");
    }
  }

  return classes.join(" ");
}
</script>
<template>
  <Drawer :title="getDrawerTitle" class="w-full max-w-[700px]">
    <Form>
      <template #permissions="slotProps">
        <Spin :spinning="loadingPermissions" wrapper-class-name="w-full">
          <Tree
            :tree-data="permissions"
            multiple
            bordered
            :default-expanded-level="0"
            :get-node-class="getNodeClass"
            v-bind="slotProps"
            value-field="id"
            label-field="title"
            icon-field="icon"
          >
            <template #node="{ value }">
              <!-- <IconifyIcon v-if="value.icon" :icon="value.icon" /> -->
              <Tag v-if="value.type === 1" color="processing">目录</Tag>
              <Tag v-if="value.type === 2" color="success">菜单</Tag>
              <Tag v-if="value.type === 3" color="yellow">按钮</Tag>
              <Tag v-if="value.type === 4" color="cyan">接口</Tag>
              {{ $t(value.title) }}
            </template>
          </Tree>
        </Spin>
      </template>
    </Form>
  </Drawer>
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
