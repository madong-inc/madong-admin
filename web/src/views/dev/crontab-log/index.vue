<script lang="ts" setup>
import type { FormProps, VxeGridListeners, VxeGridProps } from "#/adapter";

import { ref } from "vue";

import { Page } from "#/components/core/effects/common-ui";

import { DownOutlined } from "@ant-design/icons-vue";
import { Button, message, Modal } from "ant-design-vue";

import { useVxeGrid } from "#/adapter";
import { TableAction } from "#/components/table";

import { gridSchemas, searchFormSchemas } from "./data";
import { SystemCrontabLogApi, SystemCrontabLogRow } from "#/api/dev/crontab";
import { $t } from "#/locale";

const api = new SystemCrontabLogApi();

const props = defineProps({
  crontab_id: {
    type: [String, Number],
    default: undefined,
  },
});

const hasTopTableDropDownActions = ref(false);
const formOptions: FormProps = {
  ...searchFormSchemas,
  collapsed: true,
};

const gridOptions: VxeGridProps<any> = {
  ...gridSchemas,
  toolbarConfig: {
    ...gridSchemas.toolbarConfig,
    slots: {
      buttons: "toolbar-buttons",
    },
    custom: {
      // 自定义列-图标
      icon: "vxe-icon-menu",
    },
  },
  // border: false,
  height: "auto",
  keepSource: true,
  pagerConfig: {},
  proxyConfig: {
    ajax: {
      query: async ({ page }, formValues) => {
        return await api.list({
          page: page.currentPage,
          limit: page.pageSize,
          ...formValues,
          crontab_id: props.crontab_id,
        });
      },
    },
  },
};
const gridEvents: VxeGridListeners<any> = {
  checkboxChange() {
    // eslint-disable-next-line no-use-before-define
    const records = gridApi.grid.getCheckboxRecords();
    hasTopTableDropDownActions.value = records.length > 0;
  },
  checkboxAll() {
    // eslint-disable-next-line no-use-before-define
    const records = gridApi.grid.getCheckboxRecords();
    hasTopTableDropDownActions.value = records.length > 0;
  },
};
const [Grid, gridApi] = useVxeGrid({
  formOptions,
  gridOptions,
  gridEvents,
});

// 删除
const handleDelete = (row: SystemCrontabLogRow) => {
  api.remove(row.id).then(() => {
    message.success($t("common.message.success"));
    gridApi.reload();
  });
};


/**
 * 批量删除
 */
function handleMultiDelete() {
  const data = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  Modal.confirm({
    title: "提示",
    okType: "danger",
    content: `确认删除选中的${data.length}条记录吗？`,
    onOk: async () => {
      await api.remove(0, {data});
      await gridApi.query();
    },
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
              label: $t('dev.crontab_log.list.table.toolbar.delete.label'),
              type: 'primary',
              icon: 'ant-design:delete-outlined',
              show: hasTopTableDropDownActions,
              danger: true,
              auth: ['admin', 'dev:crontab_log:delete'],
              onClick:handleMultiDelete.bind(null, false)
            },
          ]"
        >
        </TableAction>
      </template>
      <template #toolbar-tools></template>
      <template #ACTION="{ row }">
        <TableAction
          :actions="[
            {
              label: $t('dev.crontab_log.list.table.columns.actions.delete.label'),
              danger: true,
              icon: 'ant-design:delete-outlined',
              auth: ['admin', 'dev:crontab_log:delete'],
              popConfirm: {
                title:  $t('dev.crontab_log.list.table.columns.actions.delete.confirm'),
                confirm: handleDelete.bind(null, row),
              },
            },
          ]"
        />
      </template>
    </Grid>
  </Page>
</template>
