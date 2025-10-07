<script lang="ts" setup>
import { computed, ref } from 'vue';

import { useDrawer, useModal } from '#/components/core/effects/common-ui';

import { Button, message } from 'ant-design-vue';

import { OnActionClickParams, type FormProps } from '#/adapter';
import {
  useVxeGrid,
  type VxeGridListeners,
  type VxeGridProps,
} from '#/adapter';
import { Icon } from '#/components/icon';
import {TableAction} from '#/components/table';

import AddUser from './add-user.vue';
import { SystemAuthApi } from '#/api/system/auth';
import { $t } from '#/locale';
import type{ SystemRoleRow } from '#/api/system/role';

const api= new SystemAuthApi();
const emit = defineEmits<{
  success: [];
}>();


const [UseModal, userModalApi] = useModal({
  connectedComponent: AddUser,
  destroyOnClose: true,
});

const record = ref();
const hasTopTableDropDownActions = ref(false);
const formOptions: FormProps = {
  collapsed: true,
  commonConfig: {
    labelWidth: 60,
  },
  schema: [
    {
      component: 'Input',
      fieldName: 'LIKE_user_name',
      label: $t('system.user.list.query.user_name'),
      componentProps: {
        placeholder: $t('system.user.list.query.placeholder.user_name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_real_name',
      label: $t('system.user.list.query.real_name'),
      componentProps: {
        placeholder: $t('system.user.list.query.placeholder.real_name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_mobile_phone',
      label: $t('system.user.list.query.mobile_phone'),
      componentProps: {
        placeholder: $t('system.user.list.query.placeholder.mobile_phone'),
        allowClear: true,
      },
    },
  ],
  handleReset: async () => {
      // eslint-disable-next-line no-use-before-define
      const { formApi, reload } = gridApi;
      await formApi.resetForm();
      const formValues = formApi.form.values;
      formApi.setLatestSubmissionValues(formValues);
      await reload(formValues);
    }
};

const gridOptions: VxeGridProps<any> = {
  columns: [
    { type: 'checkbox', width: 60 },
    { field: 'user_name', title: $t('system.user.list.table.columns.user_name') },
    { field: 'real_name', title: $t('system.user.list.table.columns.real_name') },
    { field: 'mobile_phone', title: $t('system.user.list.table.columns.mobile_phone') },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'name',
          nameTitle: $t('system.user.list.table.columns.name'),
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'delete',
            text: $t('system.user.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:role:remove'],
          },
         
        ],
      },
      field: 'operation',
      fixed: 'right',
      title: $t('system.user.list.table.columns.operation'),
      width: 200,
    },
  ],
  toolbarConfig: {
    refresh: true, // 刷新
    print: false, // 打印
    export: false, // 导出
    // custom: true, // 自定义列
    zoom: true, // 最大化最小化
    search: true,
    slots: {
      buttons: 'toolbar-buttons',
    },
    custom: {
      // 自定义列-图标
      icon: 'vxe-icon-menu',
    },
  },
  columnConfig: {
    resizable: true,//开启拖拽行宽模式
  },
  // border: false,
  height: 'auto',
  keepSource: true,
  pagerConfig: {},
  proxyConfig: {
    ajax: {
      query: async ({ page }, formValues) => {
        //
        return await api.userListByRoleId({
          page: page.currentPage,
          limit: page.pageSize,
          ...formValues,
          role_id: record.value.id,
        });
      },
    },
  },
};

/**
 * 操作栏事件
 * @param e 
 */
function onActionClick(e: OnActionClickParams<SystemRoleRow>) {
  switch (e.code) {
    case 'delete': {
      handleRemoveUserRole(e.row);
      break;
    }
  }
}

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

const [Drawer, DrawerApi] = useDrawer({
  footer: false,
  onOpenChange(isOpen) {
    record.value = isOpen ? DrawerApi.getData()?.record : {};
    console.log(record.value)

  },
});

// 添加成员
const handleAdd = () => {
  userModalApi.setData({
    record: record.value,
    gridApi,
  }).open();
};

// 删除用户角色关系
const handleRemoveUserRole = (row: any) => {
  let ids = [];
  if (row) {
    ids.push(row.id);
  } else {
    ids = gridApi.grid.getCheckboxRecords().map((item) => item.id);
  }
  const userRoles = ids.map((id) => {
    return {
      admin_id: id,
      role_id: record.value.id,
    };
  });

  api.removeUserRole(userRoles).then(() => {
    message.success($t('system.role.message.success'));
    onRefresh();
  });
};

/**
 * 刷新
 */
function onRefresh(){
  gridApi.reload();
}


/**
 * 标题处理
 */
 const getDrawerTitle = computed(() => 
  $t('system.role.assign_user.title') 
); 

</script>
<template>
  <div>
    <Drawer class="w-[60%]" :title=getDrawerTitle >
      <Grid>
        <template #toolbar-buttons>
          <TableAction
            :actions="[
              {
                label: $t('system.user.list.table.toolbar.create'),
                type: 'primary',
                icon: 'ant-design:plus-outlined',
                auth: ['admin', 'system:auth:user_role'],
                onClick: handleAdd.bind(null),
              },
              {
                label: $t('system.user.list.table.toolbar.delete.label'),
                type: 'primary',
                icon: 'ant-design:delete-outlined',
                disabled: !hasTopTableDropDownActions,
                danger:true,
                auth: ['admin', 'system:auth:remove_user_role'],
                popConfirm: {
                  title: $t('system.user.list.table.toolbar.delete.confirm'),
                  confirm: handleRemoveUserRole.bind(null, false),
                },
              },
            ]"
          >
          </TableAction>
        </template>
        <template #toolbar-tools></template>
      </Grid>
    </Drawer>
    <UseModal @success="onRefresh"/>
  </div>
</template>
