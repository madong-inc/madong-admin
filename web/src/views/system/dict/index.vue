<script lang="ts" setup>
import type {
  FormProps,
  OnActionClickParams,
  VxeGridListeners,
  VxeGridProps,
} from "#/adapter";

import { ref } from "vue";

import { Page, useDrawer, useModal } from "#/components/core/effects/common-ui";

import { DownOutlined } from "@ant-design/icons-vue";
import { Button, message } from "ant-design-vue";

import { useVxeGrid } from "#/adapter";
import { SystemDictApi, SystemDictItemApi } from "#/api/system/dict";
import type { SystemDictRow } from "#/api/system/dict";

import DictItemWrap from "./modules/dict-item-wrap.vue";
import Form from "./modules/form.vue";
import { useColumns, querySchema } from "./data";
import { Recordable } from "#/components/core/types";
import { $t } from "#/locale";
import { confirm } from "#/utils";
import { TableAction } from "#/components/table";
import EnumDict from "./modules/enum-dict.vue";

const api = new SystemDictApi();
const dictItemApi = new SystemDictItemApi();

const [Modal, modalApi] = useModal({
  connectedComponent: Form,
  destroyOnClose: true,
});

const [Drawer, drawerApi] = useDrawer({
  connectedComponent: DictItemWrap,
  destroyOnClose: true,
});

const [EnumDictDrawer, enumDrawerApi] = useDrawer({
  connectedComponent: EnumDict,
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
    schema: querySchema(),
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
    },
    columns: useColumns(onActionClick, onStatusChange),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as SystemDictRow) => {
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
    id: "system-dict-index",
  },
});

/**
 * 操作栏处理事件
 * @param param0
 */
function onActionClick({ code, row }: OnActionClickParams<SystemDictRow>) {
  switch (code) {
    case "dict_item": {
      handleDictItem(row);
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
async function onStatusChange(newStatus: number, row: SystemDictRow) {
  const status: Recordable<string> = {
    0: $t("system.dict.list.table.columns.actions.status.disabled"),
    1: $t("system.dict.list.table.columns.actions.status.enabled"),
  };
  try {
    await confirm(
      $t("system.dict.list.table.columns.actions.status.confirm", [
        row.name,
        status[newStatus.toString()]]),
      $t("system.user.list.table.columns.actions.status.title")
    );
    await api.changStatus({ id: row.id, enabled: newStatus });
    return true;
  } catch {
    return false;
  }
}

function handleCreat() {
  modalApi.setData({}).open();
}

function handleDictItem(record: SystemDictRow) {
  drawerApi.setData({ record }).open();
}

async function handleEdit(record: SystemDictRow) {
  modalApi.setData({ ...record });
  modalApi.open();
}

async function handleDelete(row: SystemDictRow) {
  await api.remove(row.id);
  await gridApi.query();
}

/**
 * 枚举字典
 */
function handleEnumDict(){
  enumDrawerApi.setData({type:"enum"}).open()
}


/**
 * 刷新
 */
function onRefresh() {
  gridApi.query();
}
</script>

<template>
  <Page auto-content-height>
    <Grid>
      <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('system.dict.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'system:dict:create'],
              onClick: handleCreat.bind(null),
            },
            {
              label: $t('system.dict.list.table.toolbar.enum'),
              type: 'primary',
              icon: 'ant-design:unordered-list-outlined',
              auth: ['admin', 'system:dict:enum'],
              onClick: handleEnumDict.bind(null),
            },
          ]"
        >
        </TableAction>
      </template>
    </Grid>
    <Modal @success="onRefresh" />
    <Drawer @success="onRefresh" />
    <EnumDictDrawer  />
  </Page>
</template>
