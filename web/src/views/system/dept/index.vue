<script lang="ts" setup>
import type {
  OnActionClickParams,
  VxeTableGridOptions,
} from '#/adapter/vxe-table';

import { Page, useModal } from '#/components/common-ui';


import { Button,Space, message } from "ant-design-vue";
import {PlusOutlined,ShrinkOutlined,ArrowsAltOutlined} from "@ant-design/icons-vue";

import { useVxeGrid } from '#/adapter/vxe-table';
import { SystemDeptApi } from '#/api/system/dept';
import type { SystemDept } from '#/api/system/dept';
import { $t } from '#/locale';

import { querySchema, useColumns } from './data';
import Form from './modules/form.vue';
import { eachTree } from '#/components/common/utils';
import { ref,h } from 'vue';
import { Recordable } from '#/components/common/types';
import { confirm } from "#/utils";


const api = new SystemDeptApi();
const isCollapse= ref<boolean>(false);

const [FormModal, formModalApi] = useModal({
  connectedComponent: Form,
  destroyOnClose: true,
});

/**
 * 编辑部门
 * @param row
 */
function onEdit(row: SystemDept) {
  formModalApi.setData(row).open();
}

/**
 * 添加下级部门
 * @param row
 */
function onAppend(row: SystemDept) {
  formModalApi.setData({ pid: row.id }).open();
}

/**
 * 创建新部门
 */
function onCreate() {
  formModalApi.setData(null).open();
}

/**
 * 删除部门
 * @param row
 */
function onDelete(row: SystemDept) {
  const hideLoading = message.loading({
    content: $t('ui.actionMessage.deleting', [row.name]),
    duration: 0,
    key: 'action_process_msg',
  });
  api.remove(row.id)
    .then(() => {
      message.success({
        content: $t('ui.actionMessage.deleteSuccess', [row.name]),
        key: 'action_process_msg',
      });
      refreshGrid();
    })
    .catch(() => {
      hideLoading();
    });
}

/**
 * 表格操作按钮的回调函数
 */
function onActionClick({
  code,
  row,
}: OnActionClickParams<SystemDept>) {
  switch (code) {
    case 'append': {
      onAppend(row);
      break;
    }
    case 'delete': {
      onDelete(row);
      break;
    }
    case 'edit': {
      onEdit(row);
      break;
    }
  }
}

const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    commonConfig: {
      labelWidth: 90,
      componentProps: {
        allowClear: true,
      },
    },
    schema: querySchema(),
    submitOnChange: true,
    // wrapperClass: 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
  },
  gridOptions: {
    columns: useColumns(onActionClick,onStatusChange),
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
      search: true,
      zoom: true,
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
 * 状态开关即将改变
 * @param newStatus 期望改变的状态值
 * @param row 行数据
 * @returns 返回false则中止改变，返回其他值（undefined、true）则允许改变
 */
 async function onStatusChange(newStatus: number, row: SystemDept) {
  const status: Recordable<string> = {
    0: $t("system.dept.list.table.columns.actions.status.disabled"),
    1: $t("system.dept.list.table.columns.actions.status.enabled"),
  };
  try {
    await confirm(
      $t("system.dept.list.table.columns.actions.status.confirm", [
        row.name,
        status[newStatus.toString()]]),
      $t("system.dept.list.table.columns.actions.status.title")
    );
    await api.changStatus({ id: row.id, enabled: newStatus });
    return true;
  } catch {
    return false;
  }
}


/**
 * 展开或者折叠
 */
const toggleCollapse = () => {
    isCollapse.value  = !isCollapse.value;
    eachTree(gridApi.grid?.getData(), (item) => (item.expand = isCollapse.value));
    gridApi.grid?.setAllTreeExpand(isCollapse.value);
  }

/**
 * 刷新表格
 */
function refreshGrid() {
  gridApi.query();
}
</script>
<template>
  <Page auto-content-height>
    <FormModal @success="refreshGrid" />
    <Grid>
      <template #toolbar-buttons>
        <Space>
          <!-- 新建按钮 -->
          <Button
            type="primary"
            v-access:code="['admin', 'system:dept:create']"
            @click="onCreate"
          >
            <PlusOutlined />
            {{ $t("system.dept.list.table.toolbar.create") }}
          </Button>

          <!-- 动态切换按钮 -->
          <Button
            @click="toggleCollapse"
            :icon="isCollapse ? h(ShrinkOutlined) : h(ArrowsAltOutlined)"
            :title="$t(`system.dept.list.table.toolbar.${isCollapse ? 'collapse' : 'expand'}`)
            "
          >
            {{
              isCollapse
                ? $t("system.dept.list.table.toolbar.collapse")
                : $t("system.dept.list.table.toolbar.expand")
            }}
          </Button>
        </Space>
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
