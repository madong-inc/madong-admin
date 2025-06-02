import { $t } from '#/locale';
import type { FormSchema, FormSchemaGetter } from '#/adapter/form';
import type { OnActionClickFn } from '#/adapter/vxe-table';
import type { SystemNoticeRow } from '#/api/system/notice';

import { DictEnum } from '#/components/common/constants';
import { getPopupContainer } from '#/components/common/utils';

import { getDictOptions } from '#/utils/dict';





/**
 * 搜索栏表单
 * @returns 
 */
export function querySchemas(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('system.tenant_package.list.query.name'),
      componentProps: {
        placeholder: $t('system.tenant_package.list.query.placeholder.name')
      }
    },
    {
      component: 'Select',
      label: $t('system.tenant_package.list.query.enabled'),
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),
        placeholder: $t('system.tenant_package.list.query.placeholder.enabled')
      },
      fieldName: 'type'
    },
  ];
}


/**
 * 定义表格字段数据
 * @param onActionClick 
 * @returns 
 */
export function useColumns(
  onActionClick: OnActionClickFn<SystemNoticeRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemNoticeRow) => PromiseLike<boolean | undefined>,): VxeTableGridOptions<SystemNoticeRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {

      title: $t('system.tenant_package.list.table.columns.name'),
      align: 'left',
      field: 'name',
      minWidth: 110,
    },
    {
      title: $t('system.tenant_package.list.table.columns.enabled'),
      field: 'enabled',
      width: 120,
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS)
      }
    },
    {
      title: $t('system.tenant_package.list.table.columns.remark'),
      align: 'left',
      field: 'remark',
    },
    {
      title: $t('system.tenant_package.list.table.columns.created_date'),
      field: 'created_date',
    },
    {
      align: 'center',
      cellRender: {
        name: 'CellOperation', // 渲染器名称
        attrs: {
          nameField: 'title', // 自定义字段（如果需要）
          onClick: onActionClick,
        },
        options: [
          {
            code: 'edit',
            text: $t('system.tenant_package.list.table.columns.actions.edit'),
            auth: ['admin', 'system:notice:edit'],
          },
          {
            code: 'delete',
            text: $t('system.tenant_package.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:notice:delete'],
          }
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.tenant_package.list.table.columns.operation'),
      width: 200,
    }
  ];
}




/**
 * 表单定义
 * @returns 
 */
export const formSchemas: FormSchemaGetter = () => [
  {
    component: 'Input',
    dependencies: {
      show: () => false,
      triggerFields: ['id'],
    },
    fieldName: 'id',
    label: $t('system.tenant_package.form.modal.id'),
  },
  {
    component: 'Input',
    fieldName: 'name',
    label: $t('system.tenant_package.form.modal.name'),
    rules: 'required',
  },

  {
    component: 'Input',
    fieldName: 'permissions',
    formItemClass: 'items-start',
    label: $t('system.tenant_package.form.modal.permissions'),
    modelPropName: 'modelValue',
    rules: 'selectRequired',
  },
  {
    component: 'RadioGroup',
    defaultValue: 1,
    label: $t('system.tenant_package.form.modal.enabled'),
    componentProps: {
      optionType: 'button',
      buttonStyle: 'solid',
      options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),
    },
    fieldName: 'enabled'
  },
  {
    component: 'Textarea',
    fieldName: 'remark',
    label: $t('system.tenant_package.form.modal.remark'),
    componentProps: {
      placeholder: $t('system.tenant_package.form.modal.placeholder.remark'),
      rows:5
    }
  },

];
