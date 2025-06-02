<script lang="ts" setup>
import type {  OnActionClickParams } from "#/adapter";

import { Page, useDrawer } from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import {columns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import Form from "./modules/form.vue";
import { SystemDataSourceApi,type SystemDataSourceRow} from "#/api/system/data-source";
import { $t } from "#/locale";

const api= new SystemDataSourceApi();

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
      checkMethod: ({ row }) => row.is_super !== 1,//顶级管理员禁止
    },
    columns:columns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemDataSourceRow) => {
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
}: OnActionClickParams<SystemDataSourceRow>) {
  switch (code) {
    case 'delete': {
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


// 删除
function handleDelete  (row: SystemDataSourceRow)  {
  api.remove(row.id).then(() => {
    message.success($t('common.message.success'));
    gridApi.reload();
  });
};

/**
 * 新增
 */
function handleAdd(){
  drawerApi.setData({}).open();
}


</script>

<template>
  <Page auto-content-height>
    <Grid>
      <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('system.data_source.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'system:data_source:create'],
              onClick: handleAdd.bind(null),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
