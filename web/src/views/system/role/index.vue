<script lang="ts" setup>
import type { Recordable } from '#/components/common/types';

import type {
  OnActionClickParams,
  VxeTableGridOptions,
} from '#/adapter/vxe-table';
import { SystemRoleApi } from '#/api/system/role';
import type { SystemRoleRow } from '#/api/system/role';

import { Page, useDrawer, useModal } from '#/components/common-ui';
import { Plus } from '#/components/common/icons';

import { Button, message, Modal } from 'ant-design-vue';

import { useVxeGrid } from '#/adapter/vxe-table';
import { $t } from '#/locale';

import { useColumns, querySchema } from './data';
import Form from './modules/form.vue';
import UserRole from './modules/user-role.vue';
import DataScope from './modules/data-scope.vue';


const api= new SystemRoleApi();

const [FormDrawer, formDrawerApi] = useDrawer({
  connectedComponent: Form,
  destroyOnClose: true,
});

const [UserRoleDrawer, userRoleDrawerApi] = useDrawer({
  connectedComponent: UserRole,
  destroyOnClose: true,
});

const [DataScopeModal, dataScopeModalApi] = useModal({
  connectedComponent: DataScope,
  destroyOnClose: true,
});

const [Grid, gridApi] = useVxeGrid({
  formOptions: {
    fieldMappingTime: [['createTime', ['startTime', 'endTime']]],
    schema: querySchema(),
    submitOnChange: true,
    collapsed:true,
  },
  gridOptions: {
    columns: useColumns(onActionClick, onStatusChange),
    height: 'auto',
    keepSource: true,
    proxyConfig: {
      ajax: {
        query: async ({ page }, formValues) => {
          return await api.list({
            page: page.currentPage,
            pageSize: page.pageSize,
            ...formValues,
          });
        },
      },
    },
    rowConfig: {
      keyField: 'id',
    },

    toolbarConfig: {
      custom: true,
      export: false,
      refresh: { code: 'query' },
      search: true,
      zoom: true,
      slots: {
        buttons: "toolbar-buttons",
      },
    },
  } as VxeTableGridOptions<SystemRoleRow>,
});

function onActionClick(e: OnActionClickParams<SystemRoleRow>) {
  switch (e.code) {
    case 'user' :{
      onUserRole(e.row);
      break;
    }
    case 'delete': {
      onDelete(e.row);
      break;
    }
    case 'edit': {
      onEdit(e.row);
      break;
    }
    case 'auth':{
      onDataScope(e.row);
      break;
    }
  }
}

/**
 * 将Antd的Modal.confirm封装为promise，方便在异步函数中调用。
 * @param content 提示内容
 * @param title 提示标题
 */
function confirm(content: string, title: string) {
  return new Promise((reslove, reject) => {
    Modal.confirm({
      content,
      onCancel() {
        reject(new Error('已取消'));
      },
      onOk() {
        reslove(true);
      },
      title,
    });
  });
}

/**
 * 状态开关即将改变
 * @param newStatus 期望改变的状态值
 * @param row 行数据
 * @returns 返回false则中止改变，返回其他值（undefined、true）则允许改变
 */
async function onStatusChange(
  newStatus: number,
  row: SystemRoleRow,
) {

  const status: Recordable<string> = {
    0: $t('system.role.list.table.columns.actions.status.disabled'),
    1: $t('system.role.list.table.columns.actions.status.enabled'),
  };

  try {

    await confirm($t('system.role.list.table.columns.actions.status.confirm', {
        name: row.name, 
        status: status[newStatus.toString()]
    }),$t('system.role.list.table.columns.actions.status.title'));

    await api.changStatus({ id:row.id,enabled: newStatus });
    return true;
  } catch {
    return false;
  }
}

function onEdit(record: SystemRoleRow) {
  formDrawerApi.setData({record,isUpdate:true}).open();
}

function onDelete(row: SystemRoleRow) {
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
      onRefresh();
    })
    .catch(() => {
      hideLoading();
    });
}

function onRefresh() {
  gridApi.query();
}

function onCreate() {
  formDrawerApi.setData({}).open();
}

function onUserRole(record:SystemRoleRow){
  userRoleDrawerApi.setData({record}).open();
}

function onDataScope(record:SystemRoleRow){
  dataScopeModalApi.setData({record}).open();
}

</script>
<template>
  <Page auto-content-height>
    <FormDrawer @success="onRefresh" />
    <UserRoleDrawer @success="onRefresh"/>
    <DataScopeModal @success="onRefresh"/>
    <Grid>
      <template #toolbar-buttons>
        <Button type="primary" @click="onCreate"  v-access:code="['admin', 'system:role:create']">
          <Plus class="size-5" />
          {{ $t('system.role.list.table.toolbar.create') }}
        </Button>
      </template>
    </Grid>
  </Page>
</template>
