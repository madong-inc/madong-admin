<script lang="ts" setup>
import type {
  FormProps,
  OnActionClickParams,
  VxeGridListeners,
  VxeGridProps,
} from "#/adapter";

import { ref,h } from "vue";

import { Page, useModal } from "#/components/core/effects/common-ui";

import { Button, message, Space } from "ant-design-vue";
import {DeleteOutlined,PlusOutlined} from "@ant-design/icons-vue";

import { useVxeGrid, isCheckboxChecked } from "#/adapter";
import {  SystemDictApi, SystemDictItemApi, SystemDictItemRow } from "#/api/system/dict";
import Form from "./modules/form.vue";
import { useColumns, querySchema } from "./data";
import { $t } from "#/locale";

const props = defineProps({
  dict_id: {
    type: [String, Number],
    default: undefined,
  },
  dict_code: {
    type: [String, Number],
    default: undefined,
  },
});

const api = new SystemDictApi();
const dictItemApi = new SystemDictItemApi();

const [Modal, modalApi] = useModal({
  connectedComponent: Form,
  destroyOnClose: true,
});

const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    commonConfig: {
      labelWidth: 80,
      componentProps: {
        allowClear: true,
      },
    },
    collapsed: true,
    schema: querySchema(),
    submitOnChange: true,
  },
  // showSearchForm: false,
  gridOptions: {
    checkboxConfig: {
      // 高亮
      highlight: true,
      // 翻页时保留选中状态
      reserve: true,
      trigger: "cell",
    },
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemDictItemRow) => {
          return await dictItemApi.list({
            page: page.currentPage,
            limit: page.pageSize,
            dict_id: props.dict_id,
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
    id: "system-dict_item-index",
  },
});

/**
 * 操作栏事件
 * @param param0
 */
function onActionClick({ code, row }: OnActionClickParams<SystemDictItemRow>) {
  switch (code) {
    case "edit": {
      onEdit(row);
      break;
    }
    case "delete": {
      onDelete(row);
      break;
    }
    default: {
      break;
    }
  }
}

// 打开新增表单
const onCreate = () => {
  modalApi
    .setData({
      dict_id: props.dict_id,
      dict_code: props.dict_code,
      is_update: false,
    })
    .open();
};

/**
 * 编辑表单
 * @param record 
 */
const onEdit = (record: SystemDictItemRow) => {
  modalApi.setData({
      record,
      dict_id: props.dict_id,
      dict_code: props.dict_code,
      is_update: true,
    })
    .open();
};

/**
 * 删除
 * @param row
 */
function onDelete(row: SystemDictItemRow) {
  dictItemApi.remove(row.id).then(() => {
    message.success( $t('system.dict_item.message.success'));
    onRefresh();
  });
}

/**
 * 批量删除
 */
function onMultiDelete() {
  const data = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  dictItemApi.remove(0, { data }).then(() => {
    message.success($t('system.dict_item.message.success'));
    onRefresh();
  });
}

/**
 * 刷新
 */
function onRefresh() {
  gridApi.reload();
}
</script>

<template>
  <Page auto-content-height>
    <Grid>
      <template #toolbar-buttons>
        <Space>
          <Button
            type="primary"
            :icon="h(PlusOutlined)"
            v-access:code="['admin', 'system:dict_item:create']"
            @click="onCreate"
          >
            {{ $t("system.dict_item.list.table.toolbar.create") }}
          </Button>
          <Button
            :disabled="!isCheckboxChecked(gridApi)"
            :icon="h(DeleteOutlined)"
            danger
            type="primary"
            v-access:code="['admin', 'system:dict_item:delete']"
            @click="onMultiDelete"
          >
            {{ $t("system.dict_item.list.table.toolbar.delete.label") }}
          </Button>
        </Space>
      </template>
    </Grid>
    <Modal  @success="onRefresh" />
  </Page>
</template>
