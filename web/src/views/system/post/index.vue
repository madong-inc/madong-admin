<script setup lang="ts">
import type { OnActionClickParams, VxeGridProps } from "#/adapter/vxe-table";

import { ref,h} from "vue";

import { Page, useDrawer } from "#/components/core/effects/common-ui";

import { Modal, Popconfirm, Space, Button } from "ant-design-vue";
import { MenuFoldOutlined, MenuUnfoldOutlined,DeleteOutlined,PlusOutlined} from "@ant-design/icons-vue";

import { useVxeGrid, isCheckboxChecked } from "#/adapter/vxe-table";

import DeptTree from "#/views/system/dept/modules/dept-tree.vue";

import { useColumns, querySchema } from "./data";
import Form from "./modules/form.vue";
import { SystemPostApi } from "#/api/system/post";
import type { SystemPostRow } from "#/api/system/post";
import { confirm } from "#/utils";
import { $t } from "#/locale";

const api = new SystemPostApi();

// 部门树
const selectDeptId = ref<string[]>([]);
const isLeftVisible = ref<Boolean>(false);

/**
 * 操作栏处理事件
 * @param param0
 */
function onActionClick({ code, row }: OnActionClickParams<SystemPostRow>) {
  switch (code) {
    case "delete": {
      onDelete(row);
      break;
    }
    case "edit": {
      onEdit(row);
      break;
    }
    default: {
      break;
    }
  }
}

const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    commonConfig: {
      labelWidth: 80,
      componentProps: {
        allowClear: true,
      },
    },
    schema: querySchema(),
    submitOnChange: true,
    handleReset: async () => {
      selectDeptId.value = [];
      // eslint-disable-next-line no-use-before-define
      const { formApi, reload } = gridApi;
      await formApi.resetForm();
      const formValues = formApi.form.values;
      formApi.setLatestSubmissionValues(formValues);
      await reload(formValues);
    },
    collapsed: true,
  },
  gridOptions: {
    checkboxConfig: {
      // 高亮
      highlight: true,
      // 翻页时保留选中状态
      reserve: true,
      trigger: "cell",
    },
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemPostRow) => {
          if (selectDeptId.value.length === 1) {
            formValues.dept_id = selectDeptId.value[0];
          } else {
            Reflect.deleteProperty(formValues, "dept_id");
          }

          return await api.list({
            page: page.currentPage,
            limit: page.pageSize,
            ...formValues,
          });
        },
      },
    },
    rowConfig: {
      keyField: "id",
    },
    toolbarConfig: {
      custom: true,
      export: false,
      refresh: { code: "query" },
      search: true,
      zoom: true,
      slots: {
        buttons: "toolbar-buttons",
      },
    },
    id: "system-post-index",
  },
});

const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});

function onCreat() {
  drawerApi.setData({});
  drawerApi.open();
}

async function onEdit(record: SystemPostRow) {
  drawerApi.setData({ ...record, isUpdate: true });
  drawerApi.open();
}

async function onDelete(row: SystemPostRow) {
  await api.remove(row.id);
  await gridApi.query();
}

/**
 * 删除
 */
async function onMultiDelete() {
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: SystemPostRow) => row.id);
  await confirm(
    $t("system.post.list.table.toolbar.delete.confirm", [ids.length]),
    $t("system.post.list.table.toolbar.delete.title")
  );
  await api.remove(0, { data: ids });
  await gridApi.query();
}

/**
 * 切换左侧栏
 */
function tooggleSidebar() {
  isLeftVisible.value = !isLeftVisible.value;
}
</script>

<template>
  <Page :auto-content-height="true" content-class="flex gap-[8px] w-full">
    <DeptTree
      v-model:select-dept-id="selectDeptId"
      class="w-[260px]"
      @reload="() => gridApi.reload()"
      @select="() => gridApi.reload()"
      v-show="isLeftVisible"
    />

    <Grid class="flex-1 overflow-hidden">
      <template #toolbar-buttons>
        <Space>
          <Button
            type="primary"
            :icon="h(PlusOutlined)"
            v-access:code="['admin', 'system:post:create']"
            @click="onCreat"
          >
            {{ $t("common.create") }}
          </Button>
          <Button
            :disabled="!isCheckboxChecked(gridApi)"
            :icon="h(DeleteOutlined)"
            danger
            type="primary"
            v-access:code="['admin', 'system:post:delete']"
            @click="onMultiDelete"
          >
            {{ $t("common.delete") }}
          </Button>
        </Space>
      </template>
      <template #toolbar-tools>
        <Button
          type="primary"
          shape="circle"
          class="vxe-button type--button size--small is--circle ml-2"
          :class="{ 'theme--primary': isLeftVisible }"
          @click="tooggleSidebar"
        >
          <MenuFoldOutlined
            :style="{ fontSize: '16px', width: '16px', height: '16px' }"
            v-if="isLeftVisible"
          />
          <MenuUnfoldOutlined
            :style="{ fontSize: '16px', width: '16px', height: '16px' }"
            v-else
          />
        </Button>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
