<script lang="ts" setup>
import type { OnActionClickParams } from "#/adapter";

import { Page, useDrawer } from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import { useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import Form from "./modules/form.vue";
import { TenantApi } from "#/api/platform/tenant";
import type { TenantRow } from "#/api/platform/tenant";
import { $t } from "#/locale";
import GranSubscription from "./modules/grant-subscription.vue";

const api = new TenantApi();

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
      checkMethod: ({ row }) => {
       return false;
      },
    },
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as TenantRow) => {
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
function onActionClick({ code, row }: OnActionClickParams<TenantRow>) {
  switch (code) {
    case "edit": {
      handleEdit(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
    case "gran_subscription":{
      handelGranSubscription(row)
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
 * 授权套餐
 * @param record 
 */
const handelGranSubscription = (record: TenantRow) => {
  granSubscriptionDrawerApi.setData({record}).open();
};


/**
 * 编辑
 * @param record
 */
const handleEdit = (record: TenantRow) => {
  drawerApi.setData({ ...record }).open();
};


/**
 * 表单弹窗
 */
const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});

/**
 * 详情弹窗
 */
const [GranSubscriptionFormDrawer, granSubscriptionDrawerApi] = useDrawer({
  connectedComponent: GranSubscription,
});


// 删除
function handleDelete(row: TenantRow) {
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
              label: $t('platform.tenant.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'platform:tenant:create'],
              onClick: handleAdd.bind(null),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
    <GranSubscriptionFormDrawer @reload="gridApi.query()" />
  </Page>
</template>
