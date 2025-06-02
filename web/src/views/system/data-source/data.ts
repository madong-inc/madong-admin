import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import { SystemDataSourceRow } from '#/api/system/data-source';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';


/***
 * 定义表格
 */
export function columns(
  onActionClick?: OnActionClickFn<SystemDataSourceRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemDataSourceRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemDataSourceRow>['columns'] {
  return [
    // { 
    //   type: 'checkbox',
    //    width: 60 
    // },
    {
      field: 'tenant_id',
      title: $t('system.data_source.list.table.columns.tenant_id'),
      align: 'center'
    },
    {
      field: 'name',
      title: $t('system.data_source.list.table.columns.name'),
      align: 'left',
      width: 200
    },
    {
      field: 'db_domain',
      title: $t('system.data_source.list.table.columns.db_domain'),
    },
    {
      field: 'db_host',
      title: $t('system.data_source.list.table.columns.db_host'),
    },
    {
      field: 'db_port',
      title: $t('system.data_source.list.table.columns.db_port'),
    },
    {
      field: 'db_name',
      title: $t('system.data_source.list.table.columns.db_name'),
      align: 'left'
    },
    {
      field: 'db_user',
      title: $t('system.data_source.list.table.columns.db_user'),
    },
    // { field: 'db_password', title: '密码' },
    {
      field: 'enabled',
      title: $t('system.data_source.list.table.columns.enabled'),
      width: 90,
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_YES_NO)
      },
    },

    {
      field: 'created_date',
      title: $t('system.data_source.list.table.columns.created_date'),
      width: 170
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'table_name',
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'delete',
            auth: ['admin', 'system:data_source:delete'],
            show: (values: SystemDataSourceRow) => {
              return ![1].includes(values.is_system);
            }
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.data_source.list.table.columns.operation'),
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
      fieldName: 'LIKE_tenant_id',
      label: $t('system.data_source.list.query.tenant_id'),
      componentProps: {
        placeholder: $t('system.data_source.list.query.placeholder.tenant_id'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('system.data_source.list.query.name'),
      componentProps: {
        placeholder: $t('system.data_source.list.query.placeholder.name'),
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
      label: $t('system.data_source.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'db_domain',
      label: $t('system.data_source.form.modal.db_domain'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.db_domain'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'db_host',
      label: $t('system.data_source.form.modal.db_host'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.db_host'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'db_port',
      label: $t('system.data_source.form.modal.db_port'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.db_port'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'db_name',
      label: $t('system.data_source.form.modal.db_name'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.db_name'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'db_user',
      label: $t('system.data_source.form.modal.db_user'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.db_user'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'db_password',
      label: $t('system.data_source.form.modal.db_password'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.db_password'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'tenant_id',
      label: $t('system.data_source.form.modal.tenant_id'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.tenant_id'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'name',
      label: $t('system.data_source.form.modal.name'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.name'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'phone',
      label: $t('system.data_source.form.modal.phone'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.phone'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'enabled',
      label: $t('system.data_source.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      }
    },
    {
      fieldName: 'sort',
      label: $t('system.data_source.form.modal.sort'),
      component: 'InputNumber',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.sort'),
        allowClear: true,
      },
      defaultValue: 0,
    },
    {
      fieldName: 'description',
      label: $t('system.data_source.form.modal.description'),
      component: 'Textarea',
      componentProps: {
        placeholder: $t('system.data_source.form.modal.placeholder.description'),
      },
    },
  ];
}
