<script setup lang="ts">
import type { OnActionClickParams, VxeGridProps } from "#/adapter/vxe-table";

import { Page, useDrawer, useModal } from "#/components/common-ui";

import { message, Modal, Popconfirm, Space } from "ant-design-vue";

import { useVxeGrid, isCheckboxChecked } from "#/adapter/vxe-table";
import { SystemTenantPackageApi } from "#/api/system/tenant";
import type { SystemTenantPackageRow } from "#/api/system/tenant";

import { querySchemas, useColumns } from "./data";
import noticeModal from "./modules/form.vue";
import { FormProps } from "#/adapter";
import { getNestedValue } from "#/utils";
import { TableAction } from "#/components/table";


const api = new SystemTenantPackageApi();


const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    commonConfig: {
      labelWidth: 80,
      componentProps: {
        allowClear: true,
      },
    },
    schema: querySchemas(),
    submitOnChange: true,
    handleReset: async () => {
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
      checkMethod: ({ row }) => row.is_active !== 1,
    },
    columns: useColumns(onActionClick),
    menuConfig: {
      body: {
        options: [
          [
            {
              code: "copy",
              name: "复制",
              prefixConfig: { icon: "vxe-icon-copy" },
              visible: true,
              disabled: false,
            },
          ],
        ],
      },
    },
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {}) => {
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
  gridEvents: {
    menuClick({ menu, row, column }) {
      const $grid = gridApi.grid;
      if ($grid) {
        switch (menu.code) {
          case "copy":
            if (row && column) {
              const value = getNestedValue(row, column.field);
              navigator.clipboard
                .writeText(value)
                .then(() => {
                  message.success("复制成功");
                })
                .catch((err) => {
                  console.error("复制失败:", err);
                });
            }
            break;
        }
      }
    },
  },
});

/**
 * 操作栏处理事件
 * @param param0
 */
function onActionClick({ code, row }: OnActionClickParams<SystemTenantPackageRow>) {
  switch (code) {
    case "edit": {
      handleEdit(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
    default: {
      break;
    }
  }
}


const [NoticeModal, drawerApi] = useDrawer({
  connectedComponent: noticeModal,
});

function handleAdd() {
  drawerApi.setData({}).open();
}

async function handleEdit(record: SystemTenantPackageRow) {
  drawerApi.setData({ record }).open();
}

async function handleDelete(row: SystemTenantPackageRow) {
  await api.remove(row.id);
  await gridApi.query();
}

function handleMultiDelete() {
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: SystemTenantPackageRow) => row.id);
  Modal.confirm({
    title: "提示",
    okType: "danger",
    content: `确认删除选中的${ids.length}条记录吗？`,
    onOk: async () => {
      await api.remove(0, ids);
      await gridApi.query();
    },
  });
}
</script>

<template>
  <Page :auto-content-height="true">
    <Grid>
      <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('system.tenant_package.list.table.toolbar.create'),
              type: 'primary',
              icon:'ant-design:plus-outlined',
              auth: ['admin', 'system:tenant_package:create'],
              onClick: handleAdd.bind(null),
            },
            {
              label: $t('system.tenant_package.list.table.toolbar.delete.label'),
              type: 'primary',
              danger: true,
              icon:'ant-design:delete-outlined',
              auth: ['admin', 'system:tenant_package:delete'],
              disabled: !isCheckboxChecked(gridApi),
              onClick:handleMultiDelete.bind(null, false)
            }
          ]"
        ></TableAction>
      </template>
    </Grid>
    <NoticeModal @reload="gridApi.query()" />
  </Page>
</template>
