<script lang="ts" setup>
import type { OnActionClickParams } from "#/adapter";

import { Page, useDrawer } from "#/components/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import { useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import Form from "./modules/form.vue";
import LogsWrapForm from "./modules/logs-wrap.vue";
import { SystemCrontabApi } from "#/api/monitor/crontab";
import type { SystemCrontabRow } from "#/api/monitor/crontab";
import { $t } from "#/locale";

const api = new SystemCrontabApi();

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
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemCrontabRow) => {
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
function onActionClick({ code, row }: OnActionClickParams<SystemCrontabRow>) {
  switch (code) {
    case "execute": {
      handleExecute(row);
      break;
    }
    case "edit": {
      handleEdit(row);
      break;
    }
    case "logs": {
      handleCrontabLogs(row);
      break;
    }
    case "resume": {
      handleResume(row);
      break;
    }
    case "pause": {
      handlePause(row);
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
 * 编辑
 * @param record
 */
const handleEdit = (record: SystemCrontabRow) => {
  drawerApi.setData({ ...record }).open();
};

/**
 * 任务日志
 * @param record
 */
const handleCrontabLogs = (record: any) => {
  logsDrawerApi.setData({ record }).open();
};

/**
 * 执行任务
 * @param row
 */
async function handleExecute(row: any) {
  let data = [];
  if (row) {
    data.push(row.id);
  } else {
    data = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  }
  await api.execute({ data });
  message.success($t('common.message.success'));
  gridApi.reload();
}

/**
 * 暂停任务
 * @param row
 */
async function handlePause(row: any) {
  let data = [];
  if (row) {
    data.push(row.id);
  } else {
    data = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  }
  await api.pause({ data });
  message.success($t('common.message.success'));
  gridApi.reload();
}

/**
 * 恢复任务
 * @param row
 */
async function handleResume(row: any) {
  let data = [];
  if (row) {
    data.push(row.id);
  } else {
    data = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  }
  await api.resume({ data });
  message.success($t('common.message.success'));
  gridApi.reload();
}

/**
 * 表单弹窗
 */
const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});

const [LogsDrawer, logsDrawerApi] = useDrawer({
  connectedComponent: LogsWrapForm,
});

// 删除
function handleDelete(row: SystemCrontabRow) {
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
              label: $t('system.crontab.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'monitor:crontab:create'],
              onClick: handleAdd.bind(null),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
    <LogsDrawer @reload="gridApi.query()" />
  </Page>
</template>
