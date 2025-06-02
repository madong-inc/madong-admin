import type { FormSchema } from '#/adapter/form';
import type { OnActionClickFn, VxeTableGridOptions } from '#/adapter/vxe-table';
import type { SystemRoleRow} from '#/api/system/role';
import { DictEnum } from '#/components/common/constants';
import { useAccess } from '#/components/common/effects/access';

import { $t } from '#/locale';
import { getDictOptions } from '#/utils/dict';

/**
 * 搜索表单
 * @returns 
 */
export function querySchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_code',
      label: $t('system.role.list.query.code'),
      componentProps:{
        placeholder: $t('system.role.list.query.placeholder.code')
      }
    },
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('system.role.list.query.name'),
       componentProps:{
        placeholder: $t('system.role.list.query.placeholder.name')
      }
    },
    {
      component: 'Select',
      componentProps: {
        allowClear: true,
        options: [
          { label: $t('common.enabled'), value: 1 },
          { label: $t('common.disabled'), value: 0 },
        ],
        placeholder: $t('system.role.list.query.placeholder.enabled')
      },
      fieldName: 'enabled',
      label: $t('system.role.list.query.enabled'),
    },
    {
      component: 'RangePicker',
      fieldName: 'BETWEEN_create_at',
      label: $t('system.role.list.query.created_date')
    },
  ];
}




/**
 * 表单定义
 * @returns 
 */
export function formSchemas(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'code',
      label: $t('system.role.form.modal.code'),
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'name',
      label: $t('system.role.form.modal.name'),
      rules: 'required',
    },
    {
      component: 'Select',
      fieldName: 'role_type',
      label: $t('system.role.form.modal.role_type'),
      defaultValue: 1,
      componentProps: {
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_ROLE_TYPE),
        class: 'w-full',
      },
      rules: 'required',

    },
    {
      component: 'RadioGroup',
      componentProps: {
        buttonStyle: 'solid',
        options: [
          { label: $t('common.enabled'), value: 1 },
          { label: $t('common.disabled'), value: 0 },
        ],
        optionType: 'button',
      },
      defaultValue: 1,
      fieldName: 'enabled',
      label: $t('system.role.form.modal.enabled'),
    },
    {
      component: 'Input',
      fieldName: 'permissions',
      formItemClass: 'items-start',
      label: $t('system.role.form.modal.permissions'),
      modelPropName: 'modelValue',
    },
    {
      component: 'Textarea',
      fieldName: 'remark',
      label: $t('system.role.form.modal.remark'),
    },
  ];
}




/**
 * 数据权限表单定义
 * @returns 
 */
export function dataScopeFormSchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'id',
      label: $t('system.role.form.modal.id'),
      dependencies: {
        triggerFields: ['id'],
        show: (_values: any) => {
          return false;
        },
      }
    },
    {
      component: 'Input',
      fieldName: 'code',
      label: $t('system.role.form.modal.code'),
      dependencies: {
        triggerFields: ['code'],
        disabled: (_values: any) => {
          return true;
        },
      }
    },
    {
      component: 'Input',
      fieldName: 'name',
      label: $t('system.role.form.modal.name'),
      dependencies: {
        triggerFields: ['name'],
        disabled: (_values: any) => {
          return true;
        },
      }
    },
    {
      component: 'Select',
      fieldName: 'data_scope',
      label: $t('system.role.form.modal.data_scope'),
      defaultValue: 1,
      componentProps: {
        options: getDictOptions(DictEnum.SYS_DATA_PERMISSION),
        class: 'w-full',
      },
      rules: 'required',

    },
    {
      component: 'Input',
      fieldName: 'permissions',
      formItemClass: 'items-start',
      label: $t('system.role.form.modal.dept_list'),
      modelPropName: 'modelValue',
      dependencies: {
        triggerFields: ['data_scope'],
        show: (_values: any) => {
          return [2].includes(_values.data_scope);
        },
      }
    },
  ];
}


/**
 * 定义表格字段
 * @param onActionClick 
 * @param onStatusChange 
 * @returns 
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemRoleRow>,
  onStatusChange?: (newStatus: any, row: SystemRoleRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemRoleRow>['columns'] {
  return [
    {
      field: 'id',
      title: $t('system.role.list.table.columns.id'),
      width: 200,
      visible:false
    },
    {
      field: 'name',
      title: $t('system.role.list.table.columns.role_name'),
      minWidth: 200,
    },
    {
      field: 'code', 
      title: $t('system.role.list.table.columns.code'), 
      align:'center',
      slots: {
        default: ({ row }) => { 
          return row.code; 
        },
      }
    },
    {
      field: 'data_scope',
      title: $t('system.role.list.table.columns.data_scope'),
      width: 200,
      cellRender:{
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_DATA_PERMISSION),
      }
    },
    {
      field: 'sort',
      title: $t('system.role.list.table.columns.sort'),
      width: 110,
    },
    {
      cellRender: {
        attrs: { 
          beforeChange: onStatusChange,
          disabled:(row:SystemRoleRow)=>{
            return !!(row.is_super_admin == 1);
          }
        },
        name: useAccess().hasAccessByCodes(['admin','system.role.list.table.columns.status']) ? 'CellSwitch' : 'CellTag',
        
      },
      field: 'enabled',
      title: $t('system.role.list.table.columns.enabled'),
      width: 100,
      
    },
    {
      field: 'remark',
      minWidth: 100,
      title: $t('system.role.list.table.columns.remark'),
      visible:false
    },
    {
      field: 'created_date',
      title: $t('system.role.list.table.columns.created_date'),
      width: 200,
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'name',
          nameTitle: $t('system.role.list.table.columns.name'),
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'edit',
            text: $t('system.role.list.table.columns.actions.edit'),
            auth: ['admin', 'system:role:edit'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super_admin);
            }
          },
          {
            code: 'delete',
            text: $t('system.role.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:role:delete'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super_admin);
            }
          },
          {
            code: 'auth',
            text: $t('system.role.list.table.columns.actions.data_scope'),
            auth: ['admin', 'system:role:scope'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super_admin);
            }
          },
          {
            code: 'user',
            text: $t('system.role.list.table.columns.actions.user'),
            auth: ['admin', 'system:role:user'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super_admin);
            }
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      title: $t('system.role.list.table.columns.operation'),
      width: 200,
    },
  ];
}
