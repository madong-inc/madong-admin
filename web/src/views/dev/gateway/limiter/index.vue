<script lang="ts" setup>
import type { OnActionClickParams } from "#/adapter";

import { Page, useDrawer } from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import { useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import Form from "./modules/form.vue";
import { GatewayLimiterApi } from "#/api/dev/gateway";
import type { GatewayLimiterRow } from "#/api/dev/gateway";
import { $t } from "#/locale";
import {confirm} from "#/utils";

const api = new GatewayLimiterApi();

const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    commonConfig: {
      labelWidth: 110,
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
    columns: useColumns(onActionClick,onStatusChange),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as GatewayLimiterRow) => {
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
function onActionClick({ code, row }: OnActionClickParams<GatewayLimiterRow>) {
  switch (code) {
    case "edit": {
      handleEdit(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
  }
}

/**
 * 状态开关即将改变
 * @param newStatus 期望改变的状态值
 * @param row 行数据
 * @returns 返回false则中止改变，返回其他值（undefined、true）则允许改变
 */
 async function onStatusChange(
  newStatus: number,
  row: User,
) {
  const status: Recordable<string> = {
    0: $t('dev.limiter.list.table.columns.actions.status.disabled'),
    1: $t('dev.limiter.list.table.columns.actions.status.enabled'),
  };
  try {
    await confirm($t('dev.limiter.list.table.columns.actions.status.confirm', {
        name: row.real_name, 
        status: status[newStatus.toString()]
    }),$t('dev.limiter.list.table.columns.actions.status.title'));

    await api.changStatus({ id:row.id,enabled: newStatus });
    return true;
  } catch {
    return false;
  }
}


/**
 * 新增
 */
function handleAdd() {
  drawerApi.setData({}).open();
}

/**
 * 编辑
 * @param record
 */
const handleEdit = (record: GatewayLimiterRow) => {
  drawerApi.setData({ ...record }).open();
};


/**
 * 表单弹窗
 */
const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});


// 删除
function handleDelete(row: GatewayLimiterRow) {
  api.remove(row.id).then(() => {
    message.success($t('common.message.success'));
    gridApi.reload();
  });
}

/**
 * 批量删除
 * @param row 
 */
async function handleMultiDelete(){
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: SystemRecycleBinRow) => row.id);
  await confirm(
    $t("dev.limiter.list.table.toolbar.delete.confirm", [ids.length]),
    $t("dev.limiter.list.table.toolbar.delete.title")
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
              label: $t('dev.limiter.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'dev:gateway_limiter:create'],
              onClick: handleAdd.bind(null),
            },
            {
              label: $t('dev.limiter.list.table.toolbar.delete.label'),
              type: 'primary',
              icon: 'ant-design:delete-outlined',
              danger: true,
              auth: ['admin', 'dev:gateway_limiter:delete'],
              disabled: !isCheckboxChecked(gridApi),
              onClick: handleMultiDelete.bind(null, false),
            }
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
