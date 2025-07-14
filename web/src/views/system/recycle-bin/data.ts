import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import { getDictOptions } from '#/utils';
import { DictEnum } from '#/components/common/constants';
import { SystemRecycleBinRow } from '#/api/system/recycle-bin';
import { $t } from '#/locale';

/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemRecycleBinRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemRecycleBinRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemRecycleBinRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'table_name',
      title: $t('system.recycle_bin.list.table.columns.table_name'),
      align: 'center'
    },
    {
      field: 'data',
      title: $t('system.recycle_bin.list.table.columns.data'),
      slots: {
        default: ({ row }) => {
          const jsonString = JSON.stringify(row.data, null, 2);
          return jsonString;
        },

      }
    },
    { field: 'ip', title: 'IP' },
    // {
    //   field: 'enabled',
    //   title: $t('system.recycle_bin.list.table.columns.enabled'),
    //   width: 90,
    //   cellRender: {
    //     name: 'CellTag',
    //     options: getDictOptions(DictEnum.SYS_YES_NO)
    //   },
    // },
        {
      field: 'created_date',
      title: $t('system.recycle_bin.list.table.columns.created_date'),
      width: 150
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'table_name',
          nameTitle: $t('system.recycle_bin.list.table.columns.table_name'),
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'detail',
            text: $t('system.recycle_bin.list.table.columns.actions.detail'),
            auth: ['admin', 'system:recycle_bin:detail']
          },
          {
            code: 'recover',
            text: $t('system.recycle_bin.list.table.columns.actions.recover'),
            auth: ['admin', 'system:recycle_bin:recover']
          },
          {
            code: 'delete',
            text: $t('system.recycle_bin.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:recycle_bin:delete']
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.recycle_bin.list.table.columns.operation'),
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
      fieldName: 'LIKE_table_name',
      label: $t('system.recycle_bin.list.query.table_name'),
      componentProps: {
        placeholder: $t('system.recycle_bin.list.query.placeholder.table_name'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'EQ_enabled',
      label: $t('system.recycle_bin.list.query.enabled'),
      componentProps: {
        placeholder: $t('system.recycle_bin.list.query.placeholder.enabled'),
        options: getDictOptions(DictEnum.SYS_YES_NO),
      },
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
      fieldName: 'table_name',
      label: $t('system.recycle_bin.form.modal.table_name'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      }
    },
    {
      fieldName: 'operate_name',
      label: $t('system.recycle_bin.form.modal.operate_name'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'ip',
      label: $t('system.recycle_bin.form.modal.ip'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'created_date',
      label: $t('system.recycle_bin.form.modal.created_date'),
      component: 'Input',
      componentProps: {
        placeholder: '',
        readOnly: true,
      },
    },
    {
      fieldName: 'enabled',
      label: $t('system.recycle_bin.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      dependencies: {
        triggerFields: ['enabled'],
        disabled: true,
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'data',
      label: $t('system.recycle_bin.form.modal.data'),
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
