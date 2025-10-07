import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type{ SystemLoginRecordRow } from '#/api/system/logs';
import { DictEnum } from '#/components/core/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';




/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemLoginRecordRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemLoginRecordRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemLoginRecordRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    { 
      field: 'user_name',
      title:$t('system.login_logs.list.table.columns.user_name'),
      cellRender: {
      },
    },
    {
      field: 'status',
      title:$t('system.login_logs.list.table.columns.status'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_OPERATION_RESULT)
      }
    },
    { 
      field: 'os',
      title:$t('system.login_logs.list.table.columns.os'),
      },
    {
       field: 'browser',
       title:$t('system.login_logs.list.table.columns.browser'),
      },
    { 
      field: 'ip',
      title:$t('system.login_logs.list.table.columns.ip'),
      },
    { 
      field: 'ip_location',
      title:$t('system.login_logs.list.table.columns.ip_location'),
    },
    { 
      field: 'created_date',
      title:$t('system.login_logs.list.table.columns.login_date'),
        width: 170 
      },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'user_name',
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'delete',
            title:$t('system.login_logs.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:logs_login:delete'],
            // show:(values:UserLoginRecord)=>{
            //   return ![1].includes(values.is_system);
            // }
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title:$t('system.login_logs.list.table.columns.operation'),
      width: 200,
    },
  ]
}

/**
 * 搜索表单
 * @returns 
 */
export function querySchemas(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_user_name',
      label:$t('system.login_logs.list.query.user_name'),
      componentProps: {
        placeholder: $t('system.login_logs.list.query.placeholder.user_name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_ip',
      label:$t('system.login_logs.list.query.ip'),
      componentProps: {
        placeholder: $t('system.login_logs.list.query.placeholder.ip'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'status',
      label:$t('system.login_logs.list.query.status'),
      componentProps: {
        placeholder: $t('system.login_logs.list.query.placeholder.status'),
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_OPERATION_RESULT)
      },
    },
    {
      component: 'RangePicker',
      fieldName: 'BETWEEN_created_at',
      label:$t('system.login_logs.list.query.created_date'),
    },
  ];
}

