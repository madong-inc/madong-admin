<script setup lang="ts">
import type { OnActionClickParams, VxeGridProps } from "#/adapter/vxe-table";

import { Page, useDrawer, useModal } from "#/components/common-ui";

import { message, Modal, Popconfirm, Space } from "ant-design-vue";

import { useVxeGrid, isCheckboxChecked } from "#/adapter/vxe-table";
import { querySchemas, useColumns } from "./data";
import detailModal from "./modules/form.vue";
import { FormProps } from "#/adapter";
import { getNestedValue } from "#/utils";
import { TableAction } from "#/components/table";
import { SystemMessageApi } from "#/api/system/message";
import type { SystemMessageRow } from "#/api/system/message";
import { router } from "#/router";

const api = new SystemMessageApi();

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
    wrapperClass: "grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
  },
  gridOptions: {
    checkboxConfig: {
      // 高亮
      highlight: true,
      // 翻页时保留选中状态
      reserve: true,
      trigger: "cell",
      checkMethod: ({ row }) => row.is_active !== 1,
    },
    columns: useColumns(onActionClick),
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
            {
              code: "related",
              name: "业务关联",
              prefixConfig: { icon: "vxe-icon-link" },
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
    // border:'inner',
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
          case "related":
            if (row && column) {
              const value = getNestedValue(row, "related_type");
              const handler = messageHandlers[value] || messageHandlers.default;
              handler(row);
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
function onActionClick({ code, row }: OnActionClickParams<SystemMessageRow>) {
  switch (code) {
    case "action": {
      const value = getNestedValue(row, "related_type");
      const handler = messageHandlers[value] || messageHandlers.default;
      handler(row);
      break;
    }
    case "detail": {
      handleDetail(row);
      break;
    }
    default: {
      break;
    }
  }
}

const [MessageDrawer, modalApi] = useDrawer({
  connectedComponent: detailModal,
});

/**
 * 消息详情
 * @param record
 */
function handleDetail(record: SystemMessageRow) {
  modalApi.setData({ record }).open();
}

/**
 * 业务跳转根据自己场景自定义添加策略
 */
const messageHandlers = {
  // 1. 待办任务
  todo: (row: SystemMessageRow) => {
    const orderId = row.related_id;
    const actionUrl = row.action_url;
    const params = JSON.parse(row.action_params);
    router.push({
      path: actionUrl,
      query: {
        business_no:params?.business_no||''
      },
    });
  },

  // 默认处理（动态 action_url）
  default: (row: SystemMessageRow) => {
    if (row.action_url) {
      const params = row.action_params
        ? `?${new URLSearchParams(row.action_params)}`
        : "";
      router.push(`${row.action_url}${params}`);
    } else {
      console.warn("No  action URL for message:", message);
    }
  },
};
</script>

<template>
  <Page :auto-content-height="true">
    <Grid :table-title="$t('system.message.list.title')"> </Grid>
    <MessageDrawer @reload="gridApi.query()" />
  </Page>
</template>
