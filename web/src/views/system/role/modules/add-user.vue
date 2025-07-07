<script lang="ts" setup>
import { computed, ref } from 'vue';

import { useModal } from '#/components/common-ui';

import { message } from 'ant-design-vue';

import { type FormProps } from '#/adapter';
import {
  useVxeGrid,
  type VxeGridListeners,
  type VxeGridProps,
} from '#/adapter';
import { SystemAuthApi } from '#/api/system/auth';
import { $t } from '#/locale';

const emit = defineEmits<{
  success: [];
}>();


const api= new SystemAuthApi();
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
  ]
};

const gridOptions: VxeGridProps<any> = {
  columns: [
    { type: 'checkbox', width: 60 },
    { field: 'user_name', title: $t('system.user.list.table.columns.user_name') },
    { field: 'real_name', title: $t('system.user.list.table.columns.real_name') },
    { field: 'mobile_phone', title: $t('system.user.list.table.columns.mobile_phone') },
  ],
  toolbarConfig: {
    refresh: true, // 刷新
    print: false, // 打印
    export: false, // 导出
    // custom: true, // 自定义列
    zoom: true, // 最大化最小化
    search: true,
    slots: {},
    custom: {
      // 自定义列-图标
      icon: 'vxe-icon-menu',
    },
  },
  // border: false,
  height: '500',
  keepSource: true,
  columnConfig: {
    resizable: true,//开启拖拽行宽模式
  },
  pagerConfig: {},
  proxyConfig: {
    ajax: {
      query: async ({ page }, formValues) => {
        return await api.userListExcludeRoleId({
          page: page.currentPage,
          limit: page.pageSize,
          ...formValues,
          role_id: record.value.id,
        });
      },
    },
  },
};
const gridEvents: VxeGridListeners<any> = {
  checkboxChange(e: any) {
    hasTopTableDropDownActions.value = e.records.length > 0;
  },
};
const [Grid, gridApi] = useVxeGrid({
  formOptions,
  gridOptions,
  gridEvents,
});

const [Modal, ModalApi] = useModal({
  onOpenChange(isOpen) {
    record.value = isOpen ? ModalApi.getData()?.record || {} : {};
  },
  onConfirm() {
    const checkedKeys = gridApi.grid
      .getCheckboxRecords()
      .map((item) => item.id);
    const userRole = checkedKeys.map((item) => {
      return {
        admin_id: item,
        role_id: record.value.id,
      };
    });
    api.saveUserRole(userRole).then(() => {
      message.success($t('system.role.message.success'));
      gridApi.reload();
      emit('success');
    });
  },
});

/**
 * 标题处理
 */
 const getTitle = computed(() => 
  $t('system.role.assign_user.append_title') 
); 



</script>
<template>
  <div>
    <Modal class="w-[60%]" :title=getTitle>
      <Grid>
        <template #toolbar-tools></template>
      </Grid>
    </Modal>
  </div>
</template>
