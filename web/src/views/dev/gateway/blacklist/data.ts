import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type { GatewayBlacklistRow } from '#/api/dev/gateway';
import { DictEnum } from '#/components/core/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';
import { useAccess } from '#/components/core/effects/access';


/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<GatewayBlacklistRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: GatewayBlacklistRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<GatewayBlacklistRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'priority',
      title: $t('dev.blacklist.list.table.columns.priority'),
      width: 60,
      visible:false
    },
    {
      field: 'name',
      title: $t('dev.blacklist.list.table.columns.name'),
      align: 'left',
      minWidth: 150
    },
    {
      field: 'ip',
      title: $t('dev.blacklist.list.table.columns.ip'),
      align: 'left',
      minWidth: 150
    },
    {
      field: 'match_type',
      title: $t('dev.blacklist.list.table.columns.match_type'),
      width: 110,
      visible: false
    },
    {
      field: 'methods',
      title: $t('dev.blacklist.list.table.columns.methods'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_REQUEST_MODE)
      },
      minWidth: 90
    },
    {
      field: 'path',
      title: $t('dev.blacklist.list.table.columns.path'),
      align: 'left',
      minWidth: 100
    },
    {
      cellRender: {
        attrs: {
          beforeChange: onStatusChange
        },
        name: useAccess().hasAccessByCodes(['admin', 'dev:gateway_blacklist:status']) ? 'CellSwitch' : 'CellTag',

      },
      field: 'enabled',
      title: $t('system.user.list.table.columns.enabled'),
      width: 100,

    },

    {
      field: 'start_date',
      title: $t('dev.blacklist.list.table.columns.start_date'),
      align: 'left',
      minWidth: 100
    },
    {
      field: 'end_date',
      title: $t('dev.blacklist.list.table.columns.end_date'),
      align: 'left',
      minWidth: 100
    },
    {
      field: 'created_date',
      title: $t('dev.blacklist.list.table.columns.created_date'),
      align: 'left',
      minWidth: 100
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'title',
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'edit',
            text: $t('dev.blacklist.list.table.columns.actions.edit'),
            auth: ['admin', 'dev:gateway_blacklist:edit'],
          },
          {
            code: 'delete',
            auth: ['admin', 'dev:gateway_blacklist:delete'],
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('dev.blacklist.list.table.columns.operation'),
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
      fieldName: 'LIKE_name',
      label: $t('dev.blacklist.list.query.name'),
      componentProps: {
        placeholder: $t('dev.blacklist.list.query.placeholder.name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_ip',
      label: $t('dev.blacklist.list.query.ip'),
      componentProps: {
        placeholder: $t('dev.blacklist.list.query.placeholder.ip'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'path',
      label: $t('dev.blacklist.list.query.path'),
      componentProps: {
        placeholder: $t('dev.blacklist.list.query.placeholder.path'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'enabled',
      label: $t('dev.blacklist.list.query.enabled'),
      componentProps: {
        placeholder: $t('dev.blacklist.list.query.placeholder.enabled'),
        options: getDictOptions(DictEnum.SYS_YES_NO),
        allowClear: true,
      },
    }
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
      label: $t('dev.blacklist.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'name',
      label: $t('dev.blacklist.form.modal.name'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.blacklist.form.modal.placeholder.name'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'match_type',
      label: $t('dev.blacklist.form.modal.match_type'),
      component: 'Select',
      defaultValue: 1,
      componentProps: {
        placeholder: $t('dev.blacklist.form.modal.placeholder.match_type'),
        allowClear: true,
        options: [
          { label: 'ip', value: 1 },
          { label: "全局", value: 0 }
        ]
      },
      rules: 'required',
    },
    {
      fieldName: 'ip',
      label: $t('dev.blacklist.form.modal.ip'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.blacklist.form.modal.placeholder.ip'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'methods',
      label: $t('dev.blacklist.form.modal.methods'),
      component: 'Select',
      componentProps: {
        placeholder: $t('dev.blacklist.form.modal.methods'),
        options: getDictOptions(DictEnum.SYS_REQUEST_MODE)
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'path',
      label: $t('dev.blacklist.form.modal.path'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.blacklist.form.modal.placeholder.path'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'enabled',
      label: $t('dev.blacklist.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'message',
      label: $t('dev.blacklist.form.modal.message'),
      component: 'Textarea',
      componentProps: {
        rows: 6,
        placeholder: $t('dev.blacklist.form.modal.placeholder.message')
      }
    },
  ];
}
