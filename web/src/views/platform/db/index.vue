<script lang="ts" setup>
import type { OnActionClickParams } from "#/adapter";

import { Page, useDrawer } from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import { useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import Form from "./modules/form.vue";
import { DbSettingApi } from "#/api/platform/db";
import type { DbSettingRow } from "#/api/platform/db";
import { $t } from "#/locale";

const api = new DbSettingApi();

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
      checkMethod: ({ row }) => row.is_default !== 1,
    },
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as DbSettingRow) => {
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
function onActionClick({ code, row }: OnActionClickParams<DbSettingRow>) {
  switch (code) {
    case "detail": {
      handleDetail(row);
      break;
    }
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
 * 新增
 */
function handleAdd() {
  drawerApi.setData({}).open();
}


/**
 * 详情
 * @param record 
 */
const handleDetail = (record: DbSettingRow) => {
  drawerApi.setData({ ...record,read:true}).open();
};


/**
 * 编辑
 * @param record
 */
const handleEdit = (record: DbSettingRow) => {
  drawerApi.setData({ ...record }).open();
};


/**
 * 表单弹窗
 */
const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});


// 删除
function handleDelete(row: DbSettingRow) {
  api.remove(row.id).then(() => {
    message.success($t('common.message.success'));
    gridApi.reload();
  });
}
</script>

<template>
  <Page auto-content-height>
    <Grid>
      <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('platform.db.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'platform:db:create'],
              onClick: handleAdd.bind(null),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
