<script lang="ts" setup>
import type {  OnActionClickParams } from "#/adapter";

import { Page} from "#/components/core/effects/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import {useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import { SystemLoginLogApi } from "#/api/system/logs";
import type { SystemLoginRecordRow } from "#/api/system/logs";
import { $t } from "#/locale";


const api= new SystemLoginLogApi();

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
      // checkMethod: ({ row }) => {
      //   return false;
      // },
    },
    columns:useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemLoginRecordRow) => {
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
    id: 'system-post-index',
  },
});



/**
 * 表格操作按钮的回调函数
 */
 function onActionClick({
  code,
  row,
}: OnActionClickParams<SystemLoginRecordRow>) {
  switch (code) {
    case 'delete': {
      handleDelete(row);
      break;
    }
  }
}

/**
 * 删除
 * @param row 
 */
function handleDelete  (row: SystemLoginRecordRow|null)  {
  let ids = [];
  if (row) {
    ids.push(row.id);
  } else {
    ids = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  }
  api.remove(0, { data: ids ?? [] }).then(() => {
    message.success($t('common.message.success'));
    gridApi.reload();
  });
};
</script>

<template>
  <Page auto-content-height>
    <Grid>
      <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('system.login_logs.list.table.toolbar.delete.label'),
              type: 'primary',
              icon: 'ant-design:delete-filled',
              danger: true,
              disabled:!isCheckboxChecked(gridApi),
              auth: ['admin', 'system:logs_login:delete'],
              onClick: handleDelete.bind(null,false),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
