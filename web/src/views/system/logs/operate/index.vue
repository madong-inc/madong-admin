<script lang="ts" setup>
import type {  OnActionClickParams } from "#/adapter";

import { Page, useDrawer} from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import {useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import {type OperateRecord, SystemOperateLogApi } from "#/api/system/logs";
import Form from "./modules/form.vue";
import { VxeUI } from "vxe-pc-ui";

import {exportExcelApi} from "#/utils";
import { $t } from "#/locale";



const api= new SystemOperateLogApi();

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
        query: async ({ page }, formValues = {} as OperateRecord) => {
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
    //导出配置
    exportConfig:{
      remote:true,
      type:'xlsx',
      types:['xlsx'],
      mode:'server',
      modes:[
        {label:'服务端导出',value:'server'},
        // {label:'当前数据(当前页的数据)',value:'current'},
      ],
      async exportMethod ({ options }) {
        const { formApi,grid} = gridApi;
        const body = {
          query:formApi?.form?.values||{},
          filename: options.filename,
          sheetName: options.sheetName,
          isHeader: options.isHeader,
          original: options.original,
          mode: options.mode,
          ids: options.mode === 'selected' ? options.data.map((item) => item.id) : [],
          fields: options.columns.map((column) => {
            return {
              field: column.field,
              title: column.title
            }
          })
        }
      await exportExcelApi(body,api)
    }
    },
    toolbarConfig: {
      custom: true,
      export: true,
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
 * 弹窗
 */
 const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});

/**
 * 表格操作按钮的回调函数
 */
 function onActionClick({
  code,
  row,
}: OnActionClickParams<OperateRecord>) {
  switch (code) {
    case 'detail': {
      handleDetail(row);
      break;
    }
    case 'delete': {
      handleDelete(row);
      break;
    }
  }
}

/**
 * 详情
 * @param record
 */
 function handleDetail(record:OperateRecord){
  drawerApi.setData({ ...record});
  drawerApi.open();
}

/**
 * 删除
 * @param row
 */
function handleDelete  (row: OperateRecord|null)  {
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
              label: $t('system.operate_logs.list.table.toolbar.delete.label'),
              type: 'primary',
              icon: 'ant-design:delete-filled',
              danger: true,
              disabled: !isCheckboxChecked(gridApi),
              auth: ['admin', 'system:logs_operate:delete'],
              onClick: handleDelete.bind(null, false),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
