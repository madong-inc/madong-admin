import { $t } from '#/locale';
import type { FormSchema, FormSchemaGetter } from '#/adapter/form';
import type { OnActionClickFn, VxeTableGridOptions } from '#/adapter/vxe-table';

import { DictEnum } from '#/components/common/constants';
import { getPopupContainer } from '#/components/common/utils';

import { getDictOptions } from '#/utils/dict';
import { TenantSubscriptionRow } from '#/api/platform/tenant-subscription';
import { TenantRow } from '#/api/platform/tenant';
import { h } from 'vue';
import { Tag } from 'ant-design-vue';





/**
 * 搜索栏表单
 * @returns 
 */
export function querySchemas(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('platform.tenant_subscription.list.query.name'),
      componentProps: {
        placeholder: $t('platform.tenant_subscription.list.query.placeholder.name')
      }
    },
    {
      component: 'Input',
      fieldName: 'LIKE_description',
      label: $t('platform.tenant_subscription.list.query.description'),
      componentProps: {
        placeholder: $t('platform.tenant_subscription.list.query.placeholder.description')
      }
    },
    {
      component: 'Select',
      label: $t('platform.tenant_subscription.list.query.enabled'),
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),
        placeholder: $t('platform.tenant_subscription.list.query.placeholder.enabled')
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
  onActionClick: OnActionClickFn<TenantSubscriptionRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: TenantSubscriptionRow) => PromiseLike<boolean | undefined>,): VxeTableGridOptions<TenantSubscriptionRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {

      title: $t('platform.tenant_subscription.list.table.columns.name'),
      align: 'left',
      field: 'name',
      minWidth: 110,
    },
    {

      title: $t('platform.tenant_subscription.list.table.columns.description'),
      align: 'left',
      field: 'description',
      minWidth: 170,
    },
    {
      title: $t('platform.tenant_subscription.list.table.columns.enabled'),
      field: 'enabled',
      width: 120,
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_ENABLED_STATUS)
      }
    },
    // {
    //   title: $t('platform.tenant_subscription.list.table.columns.remark'),
    //   align: 'left',
    //   field: 'remark',
    // },
    {
      title: $t('platform.tenant_subscription.list.table.columns.created_date'),
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
            text: $t('platform.tenant_subscription.list.table.columns.actions.edit'),
            auth: ['admin', 'platfor:tenant_subscriotion:edit'],
          },
          {
            code: 'delete',
            text: $t('platform.tenant_subscription.list.table.columns.actions.delete.label'),
            auth: ['admin', 'platfor:tenant_subscriotion:delete'],
          },
          {
            code: 'grant_menu',
            text: $t('platform.tenant_subscription.list.table.columns.actions.grant_menu'),
            auth: ['admin', 'platfor:tenant_subscriotion:grant_menu'],
          },
          {
            code: 'grant_tenant',
            text: $t('platform.tenant_subscription.list.table.columns.actions.grant_tenant'),
            auth: ['admin', 'platfor:tenant_subscriotion:grant_tenant'],
          }
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('platform.tenant_subscription.list.table.columns.operation'),
      width: 220,
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
    label: $t('platform.tenant_subscription.form.modal.id'),
  },
  {
    component: 'Input',
    fieldName: 'name',
    label: $t('platform.tenant_subscription.form.modal.name'),
    rules: 'required',
  },

  {
    component: 'InputNumber',
    fieldName: 'sort',
    defaultValue: 0,
    label: $t('platform.tenant_subscription.form.modal.sort'),
    rules: 'required',
  },

  {
    component: 'Textarea',
    fieldName: 'description',
    label: $t('platform.tenant_subscription.form.modal.description'),
    componentProps: {
      rows: 5,
    }
  },
  {
    component: 'RadioGroup',
    defaultValue: 1,
    label: $t('platform.tenant_subscription.form.modal.enabled'),
    componentProps: {
      optionType: 'button',
      buttonStyle: 'solid',
      options: getDictOptions(DictEnum.SYS_ENABLED_STATUS),
    },
    fieldName: 'enabled'
  },

];




/**
 * 表单定义
 * @returns 
 */
export const grantMenuFormSchemas: FormSchemaGetter = () => [
  {
    component: 'Input',
    dependencies: {
      show: () => false,
      triggerFields: ['id'],
    },
    fieldName: 'id',
    label: $t('platform.tenant_subscription.form.modal.id'),
  },
  {
    component: 'Input',
    fieldName: 'permissions',
    formItemClass: 'items-start',
    label: $t('platform.tenant_subscription.form.modal.permissions'),
    modelPropName: 'modelValue',
    rules: 'selectRequired',
  }
];




/***
 * 定义表格
 */
export function useGrantTenantColumns(
  onActionClick?: OnActionClickFn<TenantRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: TenantRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<TenantRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'id',
      title: $t('platform.tenant_subscription.grant_tenant.table.columns.id'),
      align: 'left',
      visible: false
    },
    {
      field: 'company_name',
      title: $t('platform.tenant_subscription.grant_tenant.table.columns.name'),
      align: 'left'
    },
    {
      field: 'isolation_mode',
      title: $t('platform.tenant_subscription.grant_tenant.table.columns.isolation_mode'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_ISOLATION_MODE)
      },
    },
    {
      field: 'expired_at',
      title: $t('platform.tenant_subscription.grant_tenant.table.columns.effective'),
      slots: {
        default: (row: any) => {
          const isPermanent = row.expired_at == '' || row.expired_at == null;
          const value = isPermanent ? '永久有效' : row.expired_at;
          return h(
            Tag,
            {
              color: isPermanent ? 'green' : 'orange' // 永久显示绿色，有期限显示橙色
            },
            value
          );
        },
      },
    }
  ]
}
