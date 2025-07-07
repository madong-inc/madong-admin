import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import { GatewayLimiterRow } from '#/api/dev/gateway';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';
import { useAccess } from '#/components/common/effects/access';


/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<GatewayLimiterRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: GatewayLimiterRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<GatewayLimiterRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'priority',
      title: $t('dev.limiter.list.table.columns.priority'),
      width: 60
    },
    {
      field: 'name',
      title: $t('dev.limiter.list.table.columns.name'),
      align: 'left',
      minWidth: 150
    },
    {
      field: 'match_type',
      title: $t('dev.limiter.list.table.columns.match_type'),
      width: 110,
      visible: false
    },
    {
      field: 'methods',
      title: $t('dev.limiter.list.table.columns.methods'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_REQUEST_MODE)
      },
      minWidth: 90
    },
    {
      field: 'path',
      title: $t('dev.limiter.list.table.columns.path'),
      align: 'left',
      minWidth: 100
    },
    {
      cellRender: {
        attrs: {
          beforeChange: onStatusChange
        },
        name: useAccess().hasAccessByCodes(['admin', 'dev:gateway_limiter:status']) ? 'CellSwitch' : 'CellTag',

      },
      field: 'enabled',
      title: $t('system.user.list.table.columns.enabled'),
      width: 100,

    },

    {
      field: 'limit_type',
      title: $t('dev.limiter.list.table.columns.limit_type'),
    },
    {
      field: 'limit_value',
      title: $t('dev.limiter.list.table.columns.limit_value')
    },
    {
      field: 'period',
      title: $t('dev.limiter.list.table.columns.period'),
      width: 110
    },
    {
      field: 'ttl',
      title: $t('dev.limiter.list.table.columns.ttl'),
      visible: false
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
            text: $t('dev.limiter.list.table.columns.actions.edit'),
            auth: ['admin', 'dev:gateway_limiter:edit'],
          },
          {
            code: 'delete',
            auth: ['admin', 'dev:gateway_limiter:delete'],
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('dev.limiter.list.table.columns.operation'),
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
      label: $t('dev.limiter.list.query.name'),
      componentProps: {
        placeholder: $t('dev.limiter.list.query.placeholder.name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'path',
      label: $t('dev.limiter.list.query.path'),
      componentProps: {
        placeholder: $t('dev.limiter.list.query.placeholder.path'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'enabled',
      label: $t('dev.limiter.list.query.enabled'),
      componentProps: {
        placeholder: $t('dev.limiter.list.query.placeholder.enabled'),
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
      label: $t('dev.limiter.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'name',
      label: $t('dev.limiter.form.modal.name'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.limiter.form.modal.placeholder.name'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'match_type',
      label: $t('dev.limiter.form.modal.match_type'),
      component: 'Select',
      defaultValue: 1,
      componentProps: {
        placeholder: $t('dev.limiter.form.modal.placeholder.match_type'),
        allowClear: true,
        options: [
          { label: 'ip', value: 1 },
          { label: "全局", value: 0 }
        ]
      },
      rules: 'required',
    },
    {
      fieldName: 'methods',
      label: $t('dev.limiter.form.modal.methods'),
      component: 'Select',
      componentProps: {
        placeholder: $t('dev.limiter.form.modal.methods'),
        options: getDictOptions(DictEnum.SYS_REQUEST_MODE)
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'path',
      label: $t('dev.limiter.form.modal.path'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.limiter.form.modal.placeholder.path'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'limit_value',
      label: $t('dev.limiter.form.modal.limit_value'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.limiter.form.modal.placeholder.limit_value'),
      },
      rules: 'required',
    },
    {
      fieldName: 'period',
      defaultValue: 1,
      label: $t('dev.limiter.form.modal.period'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.limiter.form.modal.placeholder.period'),
      },
      rules: 'required',
    },
    {
      fieldName: 'enabled',
      label: $t('dev.limiter.form.modal.enabled'),
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
      label: $t('dev.limiter.form.modal.message'),
      component: 'Textarea',
      componentProps: {
        rows: 6,
        placeholder: $t('dev.limiter.form.modal.placeholder.message')
      }
    },
  ];
}
