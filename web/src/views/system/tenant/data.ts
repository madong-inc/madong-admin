import { $t } from '#/locale';
import { z, type FormSchema, type FormSchemaGetter } from '#/adapter/form';
import type { OnActionClickFn } from '#/adapter/vxe-table';
import type { SystemTenantRow } from '#/api/system/tenant';

import { DictEnum } from '#/components/common/constants';
import { getPopupContainer } from '#/components/common/utils';

import { getDictOptions } from '#/utils/dict';
import dayjs from 'dayjs';
import { useAccess } from '#/components/common/effects/access';





/**
 * 搜索栏表单
 * @returns 
 */
export function querySchemas(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_tenant_id',
      label: $t('system.tenant.list.query.tenant_id'),
      componentProps: {
        placeholder: $t('system.tenant.list.query.placeholder.tenant_id')
      }
    },

    {
      component: 'Input',
      label: $t('system.tenant.list.query.name'),
      componentProps: {
        placeholder: $t('system.tenant.list.query.placeholder.name')
      },
      fieldName: 'LIKE_name'
    },
    {
      component: 'Input',
      label: $t('system.tenant.list.query.contact_user_name'),
      componentProps: {
        placeholder: $t('system.tenant.list.query.placeholder.contact_user_name')
      },
      fieldName: 'LIKE_contact_user_name'
    },
    {
      component: 'Input',
      label: $t('system.tenant.list.query.contact_phone'),
      componentProps: {
        placeholder: $t('system.tenant.list.query.placeholder.contact_phone')
      },
      fieldName: 'LIKE_contact_phone'
    },
    {
      component: 'Select',
      label: $t('system.tenant.list.query.enabled'),
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),
        placeholder: $t('system.tenant.list.query.placeholder.enabled')
      },
      fieldName: 'enabled'
    },
  ];
}


/**
 * 定义表格字段数据
 * @param onActionClick 
 * @returns 
 */
export function useColumns(
  onActionClick: OnActionClickFn<SystemTenantRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemTenantRow) => PromiseLike<boolean | undefined>,): VxeTableGridOptions<SystemTenantRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {

      title: $t('system.tenant.list.table.columns.tenant_id'),
      align: 'left',
      field: 'tenant_id',
      minWidth: 110,
    },
    {
      title: $t('system.tenant.list.table.columns.company_name'),
      align: 'left',
      field: 'company_name',
      minWidth: 200,
    },
    {
      cellRender: {
        attrs: {
          beforeChange: onStatusChange,
          disabled: (row: SystemTenantRow) => {
            return !!(row.is_default == 1);
          }
        },
        name: useAccess().hasAccessByCodes(['admin', 'system.tenant.status']) ? 'CellSwitch' : 'CellTag',
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),

      },
      field: 'enabled',
      title: $t('system.tenant.list.table.columns.enabled'),
      width: 100,

    },
    {
      title: $t('system.tenant.list.table.columns.contact_user_name'),
      field: 'contact_user_name',
      width: 120
    },
    {
      title: $t('system.tenant.list.table.columns.contact_phone'),
      field: 'contact_phone'
    },

    {
      title: $t('system.tenant.list.table.columns.expired_date'),
      field: 'expired_date',
      width: 140,
      slots: {
        default: ({ row }) => {
          if(row.expired_date){
            return row.expired_date;
          }
          return $t('system.tenant.list.table.columns.expired');
        },

      }
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
            text: $t('system.tenant.list.table.columns.actions.edit'),
            auth: ['admin', 'system:tenant:edit'],
            show: (_values: any) => {
              //默认
              return ![1].includes(_values.is_default);
            }
          },
           {
            code: 'user',
            text: $t('system.tenant.list.table.columns.actions.user'),
            auth: ['admin', 'system:tenant:user'],
            show: (_values: any) => {
              //默认
              return ![1].includes(_values.is_default);
            }
          },
          {
            code: 'delete',
            text: $t('system.tenant.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:tenant:delete'],
            show: (_values: any) => {
              //默认
              return ![1].includes(_values.is_default);
            }
          }
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.tenant.list.table.columns.operation'),
      width: 200,
    }
  ];
}



const defaultExpireTime = dayjs()
  .add(365, 'days')
  .startOf('day')
  .format('YYYY-MM-DD HH:mm:ss');


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
    label: $t('system.tenant.form.modal.id'),
  },
  {
    component: 'Input',
    dependencies: {
      show: () => false,
      triggerFields: [''],
    },
    fieldName: 'tenant_id',
    label: 'tenant_id',
  },
  {
    component: 'Divider',
    componentProps: {
      orientation: 'center',
    },
    fieldName: 'basic_info',
    hideLabel: true,
    renderComponentContent: () => ({
      default: () => $t('system.tenant.form.modal.divider.basic_info'),
    }),
  },
  {
    component: 'Input',
    fieldName: 'company_name',
    label: $t('system.tenant.form.modal.company_name'),
    rules: 'required',
  },

  {
    component: 'Input',
    fieldName: 'address',
    label: $t('system.tenant.form.modal.address'),
    rules: 'required',
  },
  {
    component: 'Input',
    fieldName: 'license_number',
    label: $t('system.tenant.form.modal.license_number'),
    rules: 'required',
  },
  {
    component: 'Input',
    fieldName: 'intro',
    label: $t('system.tenant.form.modal.intro'),
    rules: 'required',
  },
  {
    component: 'Input',
    fieldName: 'contact_user_name',
    label: $t('system.tenant.form.modal.contact_user_name'),
    rules: 'required',
  },
  {
    component: 'Input',
    fieldName: 'contact_phone',
    label: $t('system.tenant.form.modal.contact_phone'),
    rules: 'required',
  },
  {
    component: 'Divider',
    componentProps: {
      orientation: 'left',
    },
    fieldName: 'account_info',
    hideLabel: true,
    dependencies: {
      if: (values) => !values?.tenant_id,
      triggerFields: ['tenant_id'],
    },
    renderComponentContent: () => ({
      default: () => $t('system.tenant.form.modal.divider.account_info'),
    }),
  },
  {
    component: 'Input',
    fieldName: 'account',
    label: $t('system.tenant.form.modal.account'),
    rules: 'required',
    dependencies: {
      if: (values) => !values?.tenant_id,
      triggerFields: ['tenant_id'],
    },
  },
  {
    component: 'Input',
    fieldName: 'password',
    label: $t('system.tenant.form.modal.password'),
    rules: 'required',
    dependencies: {
      if: (values) => !values?.tenant_id,
      triggerFields: ['tenant_id'],
    },
  },
  {
    component: 'Divider',
    componentProps: {
      orientation: 'left',
    },
    fieldName: 'tenant_info',
    hideLabel: true,
    renderComponentContent: () => ({
      default: () => $t('system.tenant.form.modal.divider.tenant_info'),
    }),
  },
  {
    component: 'Select',
    componentProps: {
      getPopupContainer,
    },
    fieldName: 'package_id',
    help: $t('system.tenant.form.modal.help.package_id'),
    label: $t('system.tenant.form.modal.package_id'),
    rules: 'selectRequired',
  },
  {
    component: 'DatePicker',
    componentProps: {
      format: 'YYYY-MM-DD HH:mm:ss',
      showTime: true,
      valueFormat: 'YYYY-MM-DD HH:mm:ss',
      getPopupContainer,
    },
    defaultValue: defaultExpireTime,
    fieldName: 'expired_date',
    help: $t('system.tenant.form.modal.help.expired_at'),
    label: $t('system.tenant.form.modal.expired_at')
  },
  {
    component: 'InputNumber',
    componentProps: {
      min: -1,
    },
    defaultValue: -1,
    fieldName: 'account_count',
    help: $t('system.tenant.form.modal.help.account_count'),
    label: $t('system.tenant.form.modal.account_count'),
    renderComponentContent(model) {
      return {
        addonBefore: () =>
          model.account_count === -1 ? $t('system.tenant.form.modal.help.no_limit') : $t('system.tenant.form.modal.help.limit'),
      };
    },
    rules: 'required',
  },
  {
    component: 'Input',
    fieldName: 'domain',
    help: $t('system.tenant.form.modal.help.domain'),
    label: $t('system.tenant.form.modal.domain'),
    renderComponentContent() {
      return {
        addonBefore: () => 'http(s)://',
      };
    },
    rules: z
      .string()
      .refine(
        (domain) =>
          !(domain.startsWith('http://') || domain.startsWith('https://')),
        { message: $t('system.tenant.form.modal.placeholder.domain') },
      )
      .optional(),
  }
];
