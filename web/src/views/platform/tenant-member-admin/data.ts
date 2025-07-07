import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type{ TenantMemberAdminRow } from '#/api/platform/tenant-member';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions, getPopupContainer } from '#/utils';



/**
 * 
 * @param onActionClick 
 * @param onStatusChange 
 * @returns 
 */
export function useColumns(
  onActionClick: OnActionClickFn<TenantMemberAdminRow>,
  onStatusChange?: (newStatus: any, row: TenantMemberAdminRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<TenantMemberAdminRow>['columns'] {
  return [
    {
      type: 'checkbox',
      width: 60
    },
    {
      field: 'code',
      title: $t('platform.tenant_member_admin.list.table.columns.id'),
      width: 150,
      visible: false
    },
    {
      field: 'tenant.company_name',
      title: $t('platform.tenant_member_admin.list.table.columns.name'),
      minWidth: 90
    },
    {
      field: 'is_super',
      title: $t('platform.tenant_member_admin.list.table.columns.is_super'),
      cellRender:{
        name:'CellTag',
        options:getDictOptions(DictEnum.SYS_TENANT_ADMIN_TYPE)
      },
       minWidth: 80
    },
    {
      field: 'is_default',
      title: $t('platform.tenant_member_admin.list.table.columns.is_default'),
      cellRender: {
        name: 'CellTag',
        options:getDictOptions(DictEnum.SYS_YES_NO)
      },
    },
    {
      field: 'priority',
      title: $t('platform.tenant_member_admin.list.table.columns.priority'),
    },
    {
      field: 'created_date',
      title: $t('platform.tenant_member_admin.list.table.columns.created_date'),
      width: 150
    },
    {
      field: 'updated_date',
      title: $t('platform.tenant_member_admin.list.table.columns.updated_date'),
      width: 150,
      visible: false
    },
    {
      align: 'center',
      cellRender: {
        name: 'CellOperation',
        attrs: {
          nameField: 'name',
          onClick: onActionClick,
          maxOperations:2
        },
        options: [
          {
            code: 'edit',
            text: $t('platform.tenant_member_admin.list.table.columns.actions.edit'),
            auth: ['admin', 'platform:tenant_admin:edit'],
          },
          {
            code: 'delete',
            text: $t('platform.tenant_member_admin.list.table.columns.actions.delete.label'),
            auth: ['admin', 'platform:tenant_admin:delete'],
          }
        ],
      },
      field: 'operation',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('platform.tenant_member_admin.list.table.columns.operation'),
      width: 180,
    }
  ]
}




// 搜索表单参数定义
export function querySchema(): FormSchema[] {
  return [
    {
      fieldName: 'EQ_is_super',
      label: $t('platform.tenant_member_admin.list.query.is_super'),
      component: 'Select',
      componentProps: {
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_TENANT_ADMIN_TYPE),
        placeholder: $t('platform.tenant_member_admin.list.query.placeholder.is_super'),
      },
    },
  ]
};


// 表单参数定义
export function formSchamas(): FormSchema[] {
  return [
    {
      fieldName: 'id',
      label: $t('platform.tenant_member_admin.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'admin_id',
      label: $t('platform.tenant_member_admin.form.modal.admin_id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['admin_id'],
      },
    },
    {
      component: 'TreeSelect',
      defaultValue: undefined,
      fieldName: 'tenant_id',
      label: $t('platform.tenant_member_admin.form.modal.tenant_id'),
      componentProps: {
        placeholder: $t('platform.tenant_member_admin.form.modal.placeholder.tenant_id'),
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'is_super',
      label: $t('platform.tenant_member_admin.form.modal.is_super'),
      component: 'ApiSelect',
      defaultValue: 2,
      componentProps: {
        options: getDictOptions(DictEnum.SYS_TENANT_ADMIN_TYPE)
      },
      rules: 'selectRequired',
    },
     {
      fieldName: 'is_default',
      label: $t('platform.tenant_member_admin.form.modal.is_default'),
      component: 'RadioGroup',
      defaultValue: 0,
      componentProps: {
        optionType: 'button',
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO)
      },
      rules: 'selectRequired',
    },
  ]
};
