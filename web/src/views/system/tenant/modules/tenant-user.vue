<script lang="ts" setup>
import { computed, ref } from 'vue';

import { useDrawer, useModal } from '#/components/common-ui';

import { Button, message } from 'ant-design-vue';

import { OnActionClickParams, type FormProps } from '#/adapter';
import {
  useVxeGrid,
  type VxeGridListeners,
  type VxeGridProps,
} from '#/adapter';
import { Icon } from '#/components/icon';

import { $t } from '#/locale';
import { getDictOptions } from '#/utils';
import { DictEnum } from '#/components/common/constants';
import { SystemTenantApi } from '#/api/system/tenant';

const api= new SystemTenantApi();
const emit = defineEmits<{
  success: [];
}>();



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
    {
      field: 'sex',
      title: $t('system.user.list.table.columns.sex'),
      cellRender:{
      options:getDictOptions(DictEnum.SYS_SEX),
      name:'CellTag'
    }},
    { field: 'mobile_phone', title: $t('system.user.list.table.columns.mobile_phone') },
    // {
    //   align: 'center',
    //   cellRender: {
    //     attrs: {
    //       nameField: 'name',
    //       nameTitle: $t('system.user.list.table.columns.name'),
    //       onClick: onActionClick,
    //     },
    //     name: 'CellOperation',
    //     options: [
    //       {
    //         code: 'delete',
    //         text: $t('system.user.list.table.columns.actions.delete.label'),
    //         auth: ['admin', 'system:tenant_user:delete'],
    //       },

    //     ],
    //   },
    //   field: 'operation',
    //   fixed: 'right',
    //   title: $t('system.user.list.table.columns.operation'),
    //   width: 200,
    // },
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
        return await api.tenantUser({
          page: page.currentPage,
          limit: page.pageSize,
          ...formValues,
          tenant_id: record.value.tenant_id,
        });
      },
    },
  },
};

/**
 * 操作栏事件
 * @param e
 */
function onActionClick(e: OnActionClickParams<any>) {
  switch (e.code) {
    case 'delete': {
     
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
  },
});



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
  $t('system.tenant.user.title')
);
</script>
<template>
  <div>
    <Drawer class="w-[60%]" :title="getDrawerTitle">
      <Grid> </Grid>
    </Drawer>
  </div>
</template>
