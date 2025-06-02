<script lang="ts" setup>
import type { OnActionClickParams } from "#/adapter";

import { Page, useDrawer } from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import { useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import { SystemRecycleBinApi } from "#/api/system/recycle-bin";
import type { SystemRecycleBinRow } from "#/api/system/recycle-bin";
import Form from "./modules/form.vue";
import { $t } from "#/locale";
import { confirm } from "#/utils";

const api = new SystemRecycleBinApi();

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
    collapsed: true,
  },
  gridOptions: {
    checkboxConfig: {
      // 高亮
      highlight: true,
      // 翻页时保留选中状态
      reserve: true,
      trigger: "cell",
      checkMethod: ({ row }) => row.is_super !== 1, //顶级管理员禁止
    },
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemRecycleBinRow) => {
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

/**
 * 表格操作按钮的回调函数
 */
function onActionClick({ code, row }: OnActionClickParams<SystemRecycleBinRow>) {
  switch (code) {
    case "detail": {
      handleDetail(row);
      break;
    }
    case "recover": {
      handleRecover(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
  }
}

/**
 * 弹窗
 */
const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});

/**
 * 详情
 * @param record
 */
function handleDetail(record: SystemRecycleBinRow) {
  drawerApi.setData({ ...record });
  drawerApi.open();
}

/**
 * 还原
 * @param row
 */
function handleRecover(row: SystemRecycleBinRow) {
  api.restore({ id: row.id }).then(() => {
    message.success($t("common.message.success"));
    gridApi.reload();
  });
}

// 删除
function handleDelete(row: SystemRecycleBinRow) {
  api.remove(row.id).then(() => {
    message.success($t("common.message.success"));
    gridApi.reload();
  });
}

/**
 * 删除
 */
async function handleMultiDelete() {
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: SystemRecycleBinRow) => row.id);
  await confirm(
    $t("system.recycle_bin.list.table.toolbar.delete.confirm", [ids.length]),
    $t("system.recycle_bin.list.table.toolbar.delete.title")
  );
  await api.remove(0, { data: ids });
  message.success($t("common.message.success"));
  await gridApi.query();
}
</script>

<template>
  <Page auto-content-height>
    <Grid>
      <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('system.recycle_bin.list.table.toolbar.delete.label'),
              type: 'primary',
              icon: 'ant-design:delete-outlined',
              danger: true,
              auth: ['admin', 'system:recycle_bin:delete'],
              disabled: !isCheckboxChecked(gridApi),
              onClick: handleMultiDelete.bind(null, false),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
