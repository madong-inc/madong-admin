import { z, type FormSchema, type OnActionClickFn, type VxeTableGridOptions } from '#/adapter';
import type { TenantRow } from '#/api/platform/tenant';
import { TenantSubscriptionApi } from '#/api/platform/tenant-subscription';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions, getPopupContainer } from '#/utils';
import { Tag } from 'ant-design-vue';
import dayjs from 'dayjs';
import { h } from 'vue';

const api = new TenantSubscriptionApi();


/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<TenantRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: TenantRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<TenantRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'id',
      title: $t('platform.tenant.list.table.columns.id'),
      align: 'left',
      visible: false
    },
    {
      field: 'company_name',
      title: $t('platform.tenant.list.table.columns.company_name'),
      align: 'left'
    },
    {
      field: 'description',
      title: $t('platform.tenant.list.table.columns.description'),
      align: 'left',
      visible: false
    },
    {
      field: 'contact_person',
      title: $t('platform.tenant.list.table.columns.contact_person'),
      align: 'left',
      visible: false
    },
    {
      field: 'contact_phone',
      title: $t('platform.tenant.list.table.columns.contact_phone'),
      align: 'left',
      visible: false
    },
    {
      field: 'isolation_mode',
      title: $t('platform.tenant.list.table.columns.isolation_mode'),
      width:110,
      cellRender: {
        name: 'CellTag',
        options:getDictOptions(DictEnum.SYS_ISOLATION_MODE)
      },
    },

    {
      field: 'enabled',
      title: $t('platform.tenant.list.table.columns.enabled'),
      cellRender: {
        name: 'CellTag',
        options:getDictOptions(DictEnum.SYS_ENABLED_STATUS)
      },
    },

    {
      field: 'address',
      title: $t('platform.tenant.list.table.columns.address'),
      align: 'left',
      visible: false
    },

    {
      field: 'created_date',
      title: $t('platform.tenant.list.table.columns.created_date'),
      width: 170
    },
    {
      align:"center",
      cellRender: {
        attrs: {
          nameField: 'company_name',
          onClick: onActionClick,
          maxOperations: 2
        },
        name: 'CellOperation',
        options: [
          {
            code: 'edit',
            text: $t('platform.tenant.list.table.columns.actions.edit'),
            auth: ['admin', 'platform:tenant:edit'],
            show:(row:TenantRow)=>{
              return row?.is_default!==1;
            },
          },
          {
            code: 'delete',
            auth: ['admin', 'platform:tenant:delete'],
            show:(row:TenantRow)=>{
              return row?.is_default!==1;
            },
          },
          {
            code: 'gran_subscription',
            text: $t('platform.tenant.list.table.columns.actions.gran_subscription'),
            auth: ['admin', 'platform:tenant:gran_subscription'],
            show:(row:TenantRow)=>{
              return row?.is_default!==1;
            },
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('platform.tenant.list.table.columns.operation'),
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
      label: $t('platform.tenant.list.query.name'),
      componentProps: {
        placeholder: $t('platform.tenant.list.query.placeholder.name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_company_name',
      label: $t('platform.tenant.list.query.company_name'),
      componentProps: {
        placeholder: $t('platform.tenant.list.query.placeholder.company_name'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'type',
      label: $t('platform.tenant.list.query.type'),
      componentProps: {
        placeholder: $t('platform.tenant.list.query.placeholder.type'),
        allowClear: true,
        options: [
          { label: '其他', value: 0, color: 'yelow' },
          { label: '企业', value: 1, color: 'success' }
        ]
      },
    },
    {
      component: 'Select',
      fieldName: 'enabled',
      label: $t('platform.tenant.list.query.enabled'),
      componentProps: {
        placeholder: $t('platform.tenant.list.query.placeholder.enabled'),
        options: getDictOptions(DictEnum.SYS_YES_NO),
        allowClear: true,
      },
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
export function formSchemas(): FormSchema[] {
  return [
    {
      component: 'Input',
      dependencies: {
        show: () => false,
        triggerFields: ['id'],
      },
      fieldName: 'id',
      label: $t('platform.tenant.form.modal.id'),
    },
    {
      component: 'Input',
      dependencies: {
        show: () => false,
        triggerFields: [''],
      },
      fieldName: 'tenant_id',
      label: 'tenant_id',
      formItemClass: "col-span-1",
    },
    {
      component: 'Divider',
      componentProps: {
        orientation: 'center',
      },
      fieldName: 'basic_info',
      hideLabel: true,
      renderComponentContent: () => ({
        default: () => $t('platform.tenant.form.modal.divider.basic_info'),
      }),
    },
    {
      component: 'Input',
      fieldName: 'company_name',
      label: $t('platform.tenant.form.modal.company_name'),
      rules: 'required',
      formItemClass: "col-span-1",
    },
    {
      component: 'Select',
      fieldName: 'isolation_mode',
      label: $t('platform.tenant.form.modal.isolation_mode'),
      defaultValue:2,
      componentProps:{
        placeholder: $t('platform.tenant.form.modal.placeholder.isolation_mode'),
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_ISOLATION_MODE),
      },
      dependencies: {
        disabled: (values) => values?.id,
        triggerFields: ['id'],
      },
      rules: 'selectRequired',
      formItemClass: "col-span-1",
    },
    {
      component: 'Input',
      fieldName: 'contact_person',
      label: $t('platform.tenant.form.modal.contact_person'),
      rules: 'required',
      formItemClass: "col-span-1",
    },
    {
      component: 'Input',
      fieldName: 'contact_phone',
      label: $t('platform.tenant.form.modal.contact_phone'),
      rules: 'required',
      formItemClass: "col-span-1",
    },
    {
      component: 'Input',
      fieldName: 'license_number',
      label: $t('platform.tenant.form.modal.license_number'),
      rules: 'required',
      formItemClass: "col-span-1",
    },
    {
      component: 'DatePicker',
      componentProps: {
        format: 'YYYY-MM-DD HH:mm:ss',
        showTime: true,
        valueFormat: 'YYYY-MM-DD HH:mm:ss',
        getPopupContainer,
      },
      defaultValue: null,
      fieldName: 'expired_at',
      label: $t('platform.tenant.form.modal.expired_at'),
      formItemClass: "col-span-1",
    },
    {
      component: 'Textarea',
      fieldName: 'description',
      label: $t('platform.tenant.form.modal.description'),
      rules: 'required',
      formItemClass: "col-span-2",
    },
    {
      component: 'Input',
      fieldName: 'address',
      label: $t('platform.tenant.form.modal.address'),
      rules: 'required',
    },


    {
      component: 'Input',
      fieldName: 'domain',
      help: $t('platform.tenant.form.modal.help.domain'),
      label: $t('platform.tenant.form.modal.domain'),
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
          { message: $t('platform.tenant.form.modal.placeholder.domain') },
        )
        .optional(),
    },


    {
      component: 'ApiSelect',
      fieldName: 'gran_subscription',
      label: $t('platform.tenant.form.modal.gran_subscription'),
      componentProps:{
        mode: 'multiple',
        placeholder: $t('platform.tenant.form.modal.placeholder.gran_subscription'),
          api: () => {
          return api.list({ format: 'select', page: 1, limit: 999 });
        },
        class: 'w-full',
        allowClear: true,
      },
      rules: 'selectRequired',
    },
    
    {
      component: 'Divider',
      componentProps: {
        orientation: 'left',
      },
      fieldName: 'account_info',
      hideLabel: true,
      dependencies: {
        if: (values) => !values?.id,
        triggerFields: ['id'],
      },
      renderComponentContent: () => ({
        default: () => $t('platform.tenant.form.modal.divider.account_info'),
      }),
    },
    {
      component: 'Select',
      fieldName: 'is_create_admin',
      label: "类型",
      defaultValue:0,
      componentProps:{
        allowClear: true,
        options: [{value:0,label:"选择管理员"},{value:1,label:"新增管理员"}],
        class: 'w-full',
      },
      dependencies: {
        show: (values) => {
          return !values?.id
        },
        triggerFields: ['id'],
      },
      rules: 'selectRequired',
      formItemClass: "col-span-2",
    },
    {
      fieldName: "admin_id",
      label: $t('platform.tenant.form.modal.account'),
      component: "Select",
      defaultValue: [],
      dependencies: {
        show: (values) => {
          return !values?.id && values?.is_create_admin === 0
        },
        triggerFields: ['id','is_create_admin'],
      },
      rules: 'selectRequired',
    },
    {
      component: 'Input',
      fieldName: 'account',
      label: $t('platform.tenant.form.modal.account'),
      rules: 'required',
      dependencies: {
        show: (values) => {
          return !values?.id && values?.is_create_admin === 1
        },
        triggerFields: ['id','is_create_admin'],
      },
    },
    {
      component: 'InputPassword',
      fieldName: 'password',
      label: $t('platform.tenant.form.modal.password'),
      rules: 'required',
      dependencies: {
        show: (values) => {
          return !values?.id && values?.is_create_admin === 1
        },
        triggerFields: ['id','is_create_admin'],
      },
    },
    {
      component: 'Divider',
      componentProps: {
        orientation: 'left',
      },
      dependencies: {
        show:(values)=>values?.isolation_mode == 2,//库隔离模式显示
        triggerFields: ['id','isolation_mode'],
      },
      fieldName: 'db_info',
      hideLabel: true,
      renderComponentContent: () => ({
        default: () => $t('platform.tenant.form.modal.divider.db_info'),
      }),
    },
    {
      component: 'Select',
      componentProps: {
        getPopupContainer,
      },
      dependencies: {
        disabled: (values) => values?.id,
        show:(values)=>values?.isolation_mode == 2,//库隔离模式显示
        triggerFields: ['id','isolation_mode'],
      },
      fieldName: 'db_name',
      help: $t('platform.tenant.form.modal.help.db_id'),
      label: $t('platform.tenant.form.modal.db_id'),
      rules: 'selectRequired',
    }
  ];
}


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
      title: $t('platform.tenant.gran_subscription.table.columns.id'),
      align: 'left',
      visible: false
    },
    {
      field: 'name',
      title: $t('platform.tenant.gran_subscription.table.columns.name'),
      align: 'left',
      width:170
    },
    {
      field: 'description',
      title: $t('platform.tenant.gran_subscription.table.columns.description'),
    }
  ]
}
