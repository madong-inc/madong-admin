<script setup lang="ts">
import type { OnActionClickParams, VxeGridProps } from "#/adapter/vxe-table";

import { Page, useDrawer, useModal } from "#/components/common-ui";

import { message, Modal, Popconfirm, Space } from "ant-design-vue";

import { useVxeGrid, isCheckboxChecked } from "#/adapter/vxe-table";
import { SystemTenantApi } from "#/api/system/tenant";
import type { SystemTenantRow } from "#/api/system/tenant";

import { querySchemas, useColumns } from "./data";
import Form from "./modules/form.vue";
import { FormProps } from "#/adapter";
import { getNestedValue } from "#/utils";
import { TableAction } from "#/components/table";
import { $t } from "#/locale";
import {confirm} from "#/utils";
import { Recordable } from "#/components/common/types";
import TenantUser from "./modules/tenant-user.vue";


const api = new SystemTenantApi();


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
      checkMethod: ({ row }) => row.is_default !== 1,
    },
    columns: useColumns(onActionClick,onStatusChange),
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
function onActionClick({ code, row }: OnActionClickParams<SystemTenantRow>) {
  switch (code) {
    case "edit": {
      handleEdit(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
    case "user":{
      handleUser(row);
      break;
    }
    default: {
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
  row: SystemTenantRow,
) {
  const status: Recordable<string> = {
    0: $t('system.tenant.list.table.columns.actions.status.disabled'),
    1: $t('system.tenant.list.table.columns.actions.status.enabled'),
  };
  try {
    await confirm(
      $t('system.tenant.list.table.columns.actions.status.confirm', [row.company_name, status[newStatus.toString()]]),
      $t('system.tenant.list.table.columns.actions.status.title'));

    await api.changStatus({ id:row.id,enabled: newStatus });
    return true;
  } catch {
    return false;
  }
}


const [FormModal, drawerApi] = useDrawer({
  connectedComponent: Form,
});

const [TenantUserDrawer, tenmantUserdrawerApi] = useDrawer({
  connectedComponent: TenantUser,
});



function handleAdd() {
  drawerApi.setData({}).open();
}

function handleUser(record: SystemTenantRow){
  tenmantUserdrawerApi.setData({ record }).open();
}

async function handleEdit(record: SystemTenantRow) {
  drawerApi.setData({ record }).open();
}

async function handleDelete(row: SystemTenantRow) {
  await api.remove(row.id);
  await gridApi.query();
}

function handleMultiDelete() {
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: SystemTenantRow) => row.id);
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
              label: $t('system.tenant.list.table.toolbar.create'),
              type: 'primary',
              icon:'ant-design:plus-outlined',
              auth: ['admin', 'system:tenant:create'],
              onClick: handleAdd.bind(null),
            },
            {
              label: $t('system.tenant.list.table.toolbar.delete.label'),
              type: 'primary',
              danger: true,
              icon:'ant-design:delete-outlined',
              auth: ['admin', 'system:tenant:delete'],
              disabled: !isCheckboxChecked(gridApi),
              onClick:handleMultiDelete.bind(null, false)
            }
          ]"
        ></TableAction>
      </template>
    </Grid>
    <FormModal @reload="gridApi.query()" />
    <TenantUserDrawer />
  </Page>
</template>
