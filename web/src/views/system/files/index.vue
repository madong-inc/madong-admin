<script lang="ts" setup>
import type { OnActionClickParams } from "#/adapter";

import { Page } from "#/components/core/effects/common-ui";
import { message } from "ant-design-vue";

import { isCheckboxChecked, useVxeGrid } from "#/adapter";
import { SystemFielsApi } from "#/api/system/files";
import type { SystemFilesRow } from "#/api/system/files";
import { useColumns, querySchemas } from "./data";
import { TableAction } from "#/components/table";
import { confirm } from "#/utils";
import { $t } from "#/locale";

const api = new SystemFielsApi();

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
        query: async ({ page }, formValues = {} as SystemFilesRow) => {
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
function onActionClick({ code, row }: OnActionClickParams<SystemFilesRow>) {
  switch (code) {
    case "download": {
      handleDownload(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
  }
}

// 下载
const handleDownload = (record: any) => {
  api
    .downloadById(record.id)
    .then((result) => {
      const blob = new Blob([result]); // 确保 result 是 Blob 数据
      const blobURL = window.URL.createObjectURL(blob);
      const tempLink = document.createElement("a");
      tempLink.style.display = "none";
      tempLink.href = blobURL;
      // 从 record 中获取文件名，提供一个默认值
      const filename = record.filename || "downloaded-file";
      tempLink.setAttribute("download", filename);
      document.body.appendChild(tempLink);
      tempLink.click();
      tempLink.remove();
      window.URL.revokeObjectURL(blobURL);
    })
    .catch((err) => {
      console.error("下载失败:", err);
    });
};

// 删除
const handleDelete = (row: any) => {
  api.remove(row.id).then(() => {
    message.success($t("common.message.success"));
    gridApi.reload();
  });
};

/**
 * 批量删除
 */
async function handleMultipleDelete() {
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: SystemFilesRow) => row.id);
  await confirm(
    $t("system.files.list.table.toolbar.delete.confirm", [ids.length]),
    $t("system.files.list.table.toolbar.delete.title")
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
              label: $t('system.files.list.table.toolbar.delete.label'),
              type: 'primary',
              icon: 'ant-design:delete-outlined',
              danger: true,
              auth: ['admin', 'system:files:delete'],
              disabled: !isCheckboxChecked(gridApi),
              onClick: handleMultipleDelete.bind(null, false),
            },
          ]"
        ></TableAction>
      </template>
    </Grid>
  </Page>
</template>
