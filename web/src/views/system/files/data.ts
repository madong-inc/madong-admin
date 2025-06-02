import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type { SystemFilesRow } from '#/api/system/files';
import { $t } from '#/locale';

/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemFilesRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemFilesRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemFilesRow>['columns'] {
  return [
    {
      type: 'checkbox',
      width: 60
    },
    {
      field: 'filename',
      title: $t('system.files.list.table.columns.filename'),
      align: 'left',
      width: 200
    },
    {
      field: 'original_filename',
      title: $t('system.files.list.table.columns.original_filename'),
    },
    {
      field: 'size_info',
       title: $t('system.files.list.table.columns.size_info'),
    },
    {
      field: 'ext',
     title: $t('system.files.list.table.columns.ext'),
    },
    {
      field: 'platform',
      title: $t('system.files.list.table.columns.platform'),
    },
    {
      field: 'created_date',
      title: $t('system.files.list.table.columns.created_date'),
    },
    {
      field: 'created_name',
      title: $t('system.files.list.table.columns.created_name')
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'filename',
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'download',
            text: $t('system.files.list.table.columns.actions.download'),
            auth: ['admin', 'system:files:download']
          },
          {
            code: 'delete',
            text: $t('system.files.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:files:delete'],
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.files.list.table.columns.operation'),
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
      fieldName: 'LIKE_filename',
      label: $t('system.files.list.query.filename'),
      componentProps: {
        placeholder: $t('system.files.list.query.placeholder.filename'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_original_filename',
      label: $t('system.files.list.query.original_filename'),
      componentProps: {
        placeholder: $t('system.files.list.query.placeholder.original_filename'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_ext',
      label: $t('system.files.list.query.ext'),
      componentProps: {
        placeholder: $t('system.files.list.query.placeholder.ext'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'IN_platform',
      label: $t('system.files.list.query.platform'),
      componentProps: {
        placeholder:  $t('system.files.list.query.placeholder.platform'),
        allowClear: true,
      },
    }
  ];
}


