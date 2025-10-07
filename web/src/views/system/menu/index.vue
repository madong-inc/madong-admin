<script lang="ts" setup>
import type {
  OnActionClickParams,
  VxeGridListeners,
  VxeGridProps,
  VxeTableGridOptions,
} from "#/adapter/vxe-table";

import { Page, useDrawer, useModal } from "#/components/core/effects/common-ui";
import { IconifyIcon } from "#/components/core/icons";
import { $t } from "#/locale";

import { MenuBadge } from "#/components/core/@core/ui-kit/menu-ui";

import { Button, Space, message } from "ant-design-vue";
import { PlusOutlined, ShrinkOutlined, ArrowsAltOutlined } from "@ant-design/icons-vue";

import { useVxeGrid } from "#/adapter/vxe-table";

import { SystemMenuApi } from "#/api/system/menu";
import type { SystemMenuRow } from "#/api/system/menu";

import { querySchema, useColumns } from "./data";
import { eachTree } from "#/components/core/utils";
import Form from "./modules/form.vue";
import InterfaceForm from "./modules/initerface-list.vue";
import { record } from "zod";
import { ref, h } from "vue";

const [FormDrawer, formDrawerApi] = useDrawer({
  //连接抽离的组件
  connectedComponent: Form,
  destroyOnClose: true,
});

const [FormModal, formModalApi] = useModal({
  //连接抽离的组件
  draggable: true,
  connectedComponent: InterfaceForm,
});

const api = new SystemMenuApi();
const isCollapse = ref<boolean>(false);

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
    wrapperClass: "grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
    collapsed: true,
  },
  gridOptions: {
    columns: useColumns(onActionClick),
    height: "auto",
    keepSource: true,
    pagerConfig: {
      enabled: false,
    },
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues) => {
          return await api.list({
            format: "table_tree",
            page: page.currentPage,
            limit: 99999,
            ...formValues,
          });
        },
      },
    },
    response: {
      list: "",
    },
    rowConfig: {
      keyField: "id",
    },
    toolbarConfig: {
      custom: true,
      export: false,
      refresh: { code: "query" },
      zoom: true,
      search: true,
      slots: {
        buttons: "toolbar-buttons",
      },
    },
    treeConfig: {
      parentField: "pid",
      rowField: "id",
      transform: false,
    },
  } as VxeTableGridOptions,
});

/**
 * 操作栏处理事件
 * @param param0
 */
function onActionClick({ code, row }: OnActionClickParams<SystemMenuRow>) {
  switch (code) {
    case "save": {
      handleSelectAuth(row);
      break;
    }
    case "append": {
      handleAppend(row);
      break;
    }
    case "delete": {
      handleDelete(row);
      break;
    }
    case "edit": {
      handleEdit(row);
      break;
    }
    default: {
      break;
    }
  }
}

/**
 * 展开或者折叠
 */
function toggleCollapse() {
  isCollapse.value = !isCollapse.value;
  eachTree(gridApi.grid?.getData(), (item) => (item.expand = isCollapse.value));
  gridApi.grid?.setAllTreeExpand(isCollapse.value);
}

/**
 * 刷新
 */
function handleRefresh() {
  gridApi.query();
}

//选择权限
function handleSelectAuth(record: SystemMenuRow) {
  formModalApi
    .setData({
      record,
    })
    .open();
}

/**
 * 编辑
 * @param row
 */
function handleEdit(record: SystemMenuRow) {
  formDrawerApi
    .setData({
      record,
      isUpdate: true,
    })
    .open();
}

/**
 * 添加
 */
function handleCreate() {
  formDrawerApi.setData({}).open();
}

/**
 * 添加下一级
 * @param row
 */
function handleAppend(record: SystemMenuRow) {
  formDrawerApi
    .setData({
      record: record,
      isUpdate: false,
    })
    .open();
}

/**
 * 删除菜单
 * @param row
 */
function handleDelete(row: SystemMenuRow) {
  const hideLoading = message.loading({
    content: $t("ui.actionMessage.deleting", [row.title]),
    duration: 0,
    key: "action_process_msg",
  });
  api
    .remove(row.id)
    .then(() => {
      message.success({
        content: $t("ui.actionMessage.deleteSuccess", [row.title]),
        key: "action_process_msg",
      });
      handleRefresh();
    })
    .catch(() => {
      hideLoading();
    });
}
</script>
<template>
  <Page auto-content-height>
    <FormModal @success="handleRefresh" />
    <FormDrawer @success="handleRefresh" />
    <Grid>
      <template #toolbar-buttons>
        <Space>
          <!-- 新建按钮 -->
          <Button
            type="primary"
            v-access:code="['admin', 'system:menu:create']"
            @click="handleCreate"
          >
            <PlusOutlined />
            {{ $t("system.menu.list.table.toolbar.create") }}
          </Button>

          <!-- 动态切换按钮 -->
          <Button
            @click="toggleCollapse"
            :icon="isCollapse ? h(ShrinkOutlined) : h(ArrowsAltOutlined)"
            :title="
              $t(`system.menu.list.table.toolbar.${isCollapse ? 'collapse' : 'expand'}`)
            "
          >
            {{
              isCollapse
                ? $t("system.menu.list.table.toolbar.collapse")
                : $t("system.menu.list.table.toolbar.expand")
            }}
          </Button>
        </Space>
      </template>

      <template #title="{ row }">
        <div class="flex w-full items-center gap-1">
          <div class="size-5 flex-shrink-0">
            <IconifyIcon v-if="row.type === 3" icon="carbon:security" class="size-full" />
            <IconifyIcon
              v-else-if="row.icon"
              :icon="row.icon || 'carbon:circle-dash'"
              class="size-full"
            />
          </div>
          <span class="flex-auto">{{ $t(row.title) }}</span>
          <div class="items-center justify-end"></div>
        </div>
        <MenuBadge
          v-if="row.meta?.badgeType"
          class="menu-badge"
          :badge="row?.meta?.badge"
          :badge-type="row?.meta?.badgeType"
          :badge-variants="row?.meta?.badgeVariants"
        />
      </template>
    </Grid>
  </Page>
</template>
<style lang="scss" scoped>
.menu-badge {
  top: 50%;
  right: 0;
  transform: translateY(-50%);

  & > :deep(div) {
    padding-top: 0;
    padding-bottom: 0;
  }
}
</style>
