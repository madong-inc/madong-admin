import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type { SystemOperateRecordRow } from '#/api/system/logs';
import { $t } from '#/locale';



/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemOperateRecordRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemOperateRecordRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemOperateRecordRow>['columns'] {
  return [
    {
      type: 'checkbox',
      width: 60
    },
    {
      field: 'name',
      title: $t('system.operate_logs.list.table.columns.name'),
      align: 'left',
      width: 90,
      
    },
    {
      field: 'app',
      title: $t('system.operate_logs.list.table.columns.app'),
      width: 60
    },
    {
      field: 'ip',
      title: $t('system.operate_logs.list.table.columns.ip'),
      width: 110
    },
    {
      field: 'ip_location',
      title: $t('system.operate_logs.list.table.columns.ip_location')
    },
    {
      field: 'os',
      title: $t('system.operate_logs.list.table.columns.os')
    },
    {
      field: 'browser',
      title: $t('system.operate_logs.list.table.columns.browser')
    },
    {
      field: 'url',
      title: $t('system.operate_logs.list.table.columns.url'),
      align: 'left',
      minWidth: 170
    },
    {
      field: 'param',
      title: $t('system.operate_logs.list.table.columns.param'),
      minWidth: 170,
      slots: {
        default: ({ row }) => {
          const jsonString = JSON.stringify(row.param, null, 2);
          return jsonString;
        },

      }
    },
    {
      field: 'created_date',
      title: $t('system.operate_logs.list.table.columns.created_date'),
      width: 170
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'url',
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'detail',
            text: $t('system.operate_logs.list.table.columns.actions.detail'),
            auth: ['admin', 'system:logs_operate:detail'],
          },
          {
            code: 'delete',
            text: $t('system.operate_logs.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:logs_operate:delete'],
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.operate_logs.list.table.columns.operation'),
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
      label: $t('system.operate_logs.list.query.user_name'),
      componentProps: {
        placeholder:$t('system.operate_logs.list.query.placeholder.user_name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_ip',
      label: $t('system.operate_logs.list.query.ip'),
      componentProps: {
        placeholder:$t('system.operate_logs.list.query.placeholder.ip'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_url',
      label: $t('system.operate_logs.list.query.url'),
      componentProps: {
        placeholder:$t('system.operate_logs.list.query.placeholder.url'),
        allowClear: true,
      },
    },
    {
      component: 'RangePicker',
      fieldName: 'BETWEEN_created_at',
      label: $t('system.operate_logs.list.query.created_date'),
    },
  ];
}


/**
 * 表单定义
 * @returns 
 */
export function formSchamas(): FormSchema[] {
  return [
    {
      fieldName: 'id',
      label: 'ID',
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'app',
      label: $t('system.operate_logs.form.app'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      }
    },
    {
      fieldName: 'ip',
      label: $t('system.operate_logs.form.ip'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'class_name',
      label: $t('system.operate_logs.form.class_name'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'method',
      label: $t('system.operate_logs.form.method'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'action',
      label: $t('system.operate_logs.form.action'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'created_date',
      label: $t('system.operate_logs.form.created_date'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'user_name',
      label: $t('system.operate_logs.form.user_name'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'param',
      label: $t('system.operate_logs.form.param'),
      component: 'Textarea',
      componentProps: {
        placeholder: '',
        readOnly: true,
        autoSize: { minRows: 3, maxRows: 5 }, // 自动高度
        style: {
          backgroundColor: '#f6f8fa',   // 浅灰背景
          border: '1px solid #e0e0e0',  // 边框颜色
          borderRadius: 4,
          fontFamily: 'monospace',       // 等宽字体
          cursor: 'text'                 // 保持文本光标
        }
      },
    },
    {
      fieldName: 'result',
      label: $t('system.operate_logs.form.result'),
      component: 'Textarea',
      componentProps: {
        placeholder: '',
        readOnly: true,
        autoSize: { minRows: 8, maxRows: 15 }, // 自动高度
        style: {
          backgroundColor: '#f6f8fa',   // 浅灰背景
          border: '1px solid #e0e0e0',  // 边框颜色
          borderRadius: 4,
          fontFamily: 'monospace',       // 等宽字体
          cursor: 'text'                 // 保持文本光标
        }
      },
    }
  ];
}
