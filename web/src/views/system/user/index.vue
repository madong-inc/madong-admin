<script setup lang="ts">
import type { FormProps } from "#/adapter/form";

import type { OnActionClickParams, VxeGridProps } from "#/adapter/vxe-table";

import { ref } from "vue";

import { Page, useDrawer, useModal } from "#/components/core/effects/common-ui";


import { Modal, Popconfirm, Space,Button, message } from "ant-design-vue";
import {MenuFoldOutlined,MenuUnfoldOutlined} from "@ant-design/icons-vue";

import { useVxeGrid,isCheckboxChecked} from "#/adapter/vxe-table";

import DeptTree from "#/views/system/dept/modules/dept-tree.vue";

import { useColumns, querySchema } from "./data";
import Form from "./modules/form.vue";
import { SystemUserApi ,type User} from "#/api/system/user";
import { Recordable } from "#/components/core/types";
import { $t } from "#/locale";
import {confirm} from "#/utils";
import { TableAction } from "#/components/table";


const api = new SystemUserApi();

// 部门树
const selectDeptId = ref<string[]>([]);
const isLeftVisible = ref<Boolean>(false);


/**
 * 操作栏处理事件
 * @param param0
 */
function onActionClick({ code, row }: OnActionClickParams<User>) {
  switch (code) {
    case "delete": {
      onDelete(row);
      break;
    }
    case "edit": {
      onEdit(row);
      break;
    }
    case "locked": {
      onLocked(row);
      break;
    }
    case "un_locked": {
      onUnLocked(row);
      break;
    }
    case "reset_password": {
      onResetPassword(row);
      break;
    }
    default: {
      break;
    }
  }
}

const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    commonConfig: {
      labelWidth: 80,
      componentProps: {
        allowClear: true,
      },
    },
    schema: querySchema(),
    submitOnChange: false,
    handleReset: async () => {
      selectDeptId.value = [];
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
      checkMethod: ({ row }) => row.is_super !== 1,//顶级管理员禁止
    },
    columns: useColumns(onActionClick, onStatusChange),
    height: "auto",
    keepSource: true,
    pagerConfig: {},
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues = {} as User) => {
          if (selectDeptId.value.length === 1) {
            formValues.dept_id = selectDeptId.value[0];
          } else {
            Reflect.deleteProperty(formValues, "dept_id");
          }

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
      export: true,
      refresh: { code: "query" },
      search: true,
      zoom: true,
      slots: {
        buttons: "toolbar-buttons",
      },
    },
    id: 'system-post-index',
  },
});

const [Drawer, drawerApi] = useDrawer({
  connectedComponent: Form,
});


/**
 * 状态开关即将改变
 * @param newStatus 期望改变的状态值
 * @param row 行数据
 * @returns 返回false则中止改变，返回其他值（undefined、true）则允许改变
 */
 async function onStatusChange(
  newStatus: number,
  row: User,
) {
  const status: Recordable<string> = {
    0: $t('system.user.list.table.columns.actions.status.disabled'),
    1: $t('system.user.list.table.columns.actions.status.enabled'),
  };
  try {
    await confirm($t('system.user.list.table.columns.actions.status.confirm', {
        name: row.real_name, 
        status: status[newStatus.toString()]
    }),$t('system.user.list.table.columns.actions.status.title'));

    await api.changStatus({ id:row.id,enabled: newStatus });
    return true;
  } catch {
    return false;
  }
}


function onCreat() {
  drawerApi.setData({});
  drawerApi.open();
}

async function onEdit(record: User) {
  drawerApi.setData({ ...record, isUpdate: true });
  drawerApi.open();
}

async function onDelete(row: User) {
  await api.remove(row.id);
  await gridApi.query();
}


/**
 * 锁定账户
 * @param row
 */
async function onLocked(row:User){
    await confirm(
      $t('system.user.list.table.columns.actions.locked.confirm',{name:row.real_name}),
      $t('system.user.list.table.columns.actions.locked.title')
    );
  const data = [];
  if (row && row.id) {
    data.push(row.id);
  }

  await api.locked({
    data,
  }).then(() => {
    message.success($t('system.user.success.locked'));
    gridApi.reload();
  });

}

/**
 * 取消锁定
 * @param row
 */
async function onUnLocked(row:User){
  await confirm(
      $t('system.user.list.table.columns.actions.un_locked.confirm',{name:row.real_name}),
      $t('system.user.list.table.columns.actions.un_locked.title')
    );
  const data = [];
  if (row && row.id) {
    data.push(row.id);
  }
  api.unLocked({
    data,
  }).then((data) => {
    message.success($t('system.user.success.un_locked'));
    gridApi.reload();
  });

}

/**
 * 重置密码
 * @param row
 */
async function onResetPassword(row:User){
  let ids = [];
  if (row) {
    ids.push(row.id);
  } else {
    ids = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  }
  api.resetPassword({ ids }).then(() => {
    message.success($t('system.user.success.reset_password'));
    gridApi.reload();
  });
}



/**
 * 删除
 */
function onMultiDelete() {
  const rows = gridApi.grid.getCheckboxRecords();
  const ids = rows.map((row: User) => row.id);
  Modal.confirm({
    title:$t('system.user.list.table.toolbar.delete.title'),
    okType: 'danger',
    content: $t('system.user.list.table.toolbar.delete.confirm',{length:ids.length}),
    onOk: async () => {
      await api.remove(0,{data:ids});
      await gridApi.query();
    },
  });
}

/**
 * 切换左侧栏
 */
function tooggleSidebar() {
  isLeftVisible.value = !isLeftVisible.value;
}
</script>

<template>
  <Page :auto-content-height="true" content-class="flex gap-[8px] w-full">
    <DeptTree
      v-model:select-dept-id="selectDeptId"
      class="w-[260px]"
      @reload="() => gridApi.reload()"
      @select="() => gridApi.reload()"
      v-show="isLeftVisible"
    />

    <Grid class="flex-1 overflow-hidden">
       <template #toolbar-buttons>
        <TableAction
          :actions="[
            {
              label: $t('system.user.list.table.toolbar.create'),
              type: 'primary',
              icon: 'ant-design:plus-outlined',
              auth: ['admin', 'system:user:create'],
              onClick: onCreat.bind(null),
            },
            {
              label: $t('system.user.list.table.toolbar.delete.label'),
              type: 'primary',
              danger:true,
              icon: 'ant-design:delete-outlined',
              auth: ['admin', 'system:user.delete'],
              disabled:!isCheckboxChecked(gridApi),
              onClick: onMultiDelete.bind(null),
            },
          ]"
        ></TableAction>
      </template>

      <template #toolbar-tools>
        <Button
          type="primary"
          shape="circle"
          class="vxe-button type--button size--small is--circle ml-2"
          :class="{ 'theme--primary': isLeftVisible }"
          @click="tooggleSidebar"
        >
          <MenuFoldOutlined
            :style="{ fontSize: '16px', width: '16px', height: '16px' }"
            v-if="isLeftVisible"
          />
          <MenuUnfoldOutlined
            :style="{ fontSize: '16px', width: '16px', height: '16px' }"
            v-else
          />
        </Button>
      </template>
    </Grid>
    <Drawer @reload="gridApi.query()" />
  </Page>
</template>
