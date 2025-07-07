import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type { DbSettingRow } from '#/api/platform/db';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';


/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<DbSettingRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: DbSettingRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<DbSettingRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'id',
      title: $t('platform.db.list.table.columns.id'),
      align: 'left',
      visible: false
    },
    {
      field: 'name',
      title: $t('platform.db.list.table.columns.name'),
      align: 'left'
    },
    {
      field: 'database',
      title: $t('platform.db.list.table.columns.database'),
      align: 'left'
    },
    {
      field: 'driver',
      title: $t('platform.db.list.table.columns.driver'),
      align: 'left'
    },
    {
      field: 'host',
      title: $t('platform.db.list.table.columns.host'),
      align: 'left'
    },
    {
      field: 'enabled',
      title: $t('platform.db.list.table.columns.enabled'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS)
      },
    },

    {
      field: 'created_date',
      title: $t('platform.db.list.table.columns.created_date'),
      width: 170
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'title',
          onClick: onActionClick,
          maxOperations: 3
        },
        name: 'CellOperation',
        options: [
          {
            code: 'detail',
            text: $t('platform.db.list.table.columns.actions.detail'),
            show:(row:DbSettingRow)=>{
              return row?.is_default!==1;
            },
            auth: ['admin', 'platform:db:detail'],
          },
          // {
          //   code: 'edit',
          //   text: $t('platform.db.list.table.columns.actions.edit'),
          //   auth: ['admin', 'platform:db:edit'],
          // },
          {
            code: 'delete',
            show:(row:DbSettingRow)=>{
              return row?.is_default!==1;
            },
            auth: ['admin', 'platform:db:delete'],
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('platform.db.list.table.columns.operation'),
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
      fieldName: 'LIKE_database',
      label: $t('platform.db.list.query.database'),
      componentProps: {
        placeholder: $t('platform.db.list.query.placeholder.database'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'driver',
      label: $t('platform.db.list.query.driver'),
      componentProps: {
        placeholder: $t('platform.db.list.query.placeholder.driver'),
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_DB_TYPE),
      },
    },
    {
      component: 'Select',
      fieldName: 'enabled',
      label: $t('platform.db.list.query.enabled'),
      componentProps: {
        placeholder: $t('platform.db.list.query.placeholder.enabled'),
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
      label: $t('platform.db.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'name',
      label: $t('platform.db.form.modal.name'),
      component: 'Input',
      componentProps: {
        placeholder: $t('platform.db.form.modal.placeholder.name'),
        allowClear: true,
      },
      formItemClass: 'col-span-8',
      rules: 'required',
    },
    {
      fieldName: 'host',
      label: $t('platform.db.form.modal.host'),
      component: 'Input',
      componentProps: {
        placeholder: $t('platform.db.form.modal.placeholder.host'),
        allowClear: true
      },
      formItemClass: 'col-span-8',
       rules: 'required',
    },
    {
      fieldName: 'port',
      label: $t('platform.db.form.modal.port'),
      component: 'Input',
      defaultValue: 3306,
      componentProps: {
        placeholder: $t('platform.db.form.modal.placeholder.port'),
      },
      formItemClass: 'col-span-8',
      rules: 'required',
    },
        {
      fieldName: 'driver',
      label: $t('platform.db.form.modal.driver'),
      component: 'Select',
      defaultValue: 'mysql',
      componentProps: {
        allowClear: true,
        placeholder: $t('platform.db.form.modal.driver'),
        options: getDictOptions(DictEnum.SYS_DB_TYPE)
      },
      formItemClass: 'col-span-6',
      rules: 'selectRequired',
    },
    {
      fieldName: 'database',
      label: $t('platform.db.form.modal.database'),
      component: 'Input',
      componentProps: {
        placeholder: $t('platform.db.form.modal.placeholder.database'),
        allowClear: true,
      },
      formItemClass: 'col-span-6',
      rules: 'required',
    },
    {
      fieldName: 'username',
      label: $t('platform.db.form.modal.username'),
      component: 'Input',
      defaultValue: '',
      componentProps: {
        placeholder: $t('platform.db.form.modal.placeholder.username'),
      },
      formItemClass: 'col-span-6',
      rules: 'required',
    },
    {
      fieldName: 'password',
      label: $t('platform.db.form.modal.password'),
      component: 'InputPassword',
      defaultValue: '',
      componentProps: {
        placeholder: $t('platform.db.form.modal.placeholder.password'),
      },
      formItemClass: 'col-span-6',
      rules: 'required',
    },
    {
      fieldName: 'enabled',
      label: $t('platform.db.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),
        optionType: 'button',
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'description',
      label: $t('platform.db.form.modal.description'),
      component: 'Textarea',
      componentProps: {
        rows:6,
        placeholder: $t('platform.db.form.modal.placeholder.description')
      }
    },
  ];
}
