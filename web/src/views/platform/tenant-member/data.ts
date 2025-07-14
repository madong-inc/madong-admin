
import { z, type FormSchema } from '#/adapter/form';
import type { OnActionClickFn, VxeTableGridOptions } from '#/adapter/vxe-table';
import { User } from '#/api/system/user';

import { DictEnum } from '#/components/common/constants';
import { getPopupContainer } from '#/components/common/utils';
import { $t } from '#/locale';

import { getDictOptions } from '#/utils/dict';
import { useAccess } from '#/components/common/effects/access';
import type { TenantMemberRow } from '#/api/platform/tenant-member';
import { h } from 'vue';
import { Tag } from 'ant-design-vue';




/**
 * 定义搜索表单
 * @returns 
 */
export function querySchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_user_name',
      label: $t('system.user.list.query.user_name'),
      componentProps: {
        placeholder: $t('system.user.list.query.placeholder.user_name')
      }
    },
    {
      component: 'Input',
      fieldName: 'LIKE_real_name',
      label: $t('system.user.list.query.real_name'),
      componentProps: {
        placeholder: $t('system.user.list.query.placeholder.real_name')
      }
    },
    {
      component: 'Input',
      fieldName: 'mobile_phone',
      label: $t('system.user.list.query.mobile_phone'),
      componentProps: {
        placeholder: $t('system.user.list.query.placeholder.mobile_phone')
      }
    },
    {
      component: 'Select',
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_YES_NO),
        placeholder: $t('system.user.list.query.placeholder.enabled')
      } as const,
      fieldName: 'enabled',
      label: $t('system.user.list.query.enabled'),
    },
    {
      component: 'RangePicker',
      fieldName: 'BETWEEN_created_at',
      label: $t('system.user.list.query.created_date'),
    },
  ];
}


/**
 * 定义表格字段数据
 * @param onActionClick 
 * @returns 
 */
export function useColumns(
  onActionClick: OnActionClickFn<TenantMemberRow>,
  onStatusChange?: (newStatus: any, row: TenantMemberRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<TenantMemberRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      field: 'user_name',
      title: $t('system.user.list.table.columns.user_name'),
      minWidth: 80,
    },
    {
      field: 'real_name',
      title: $t('system.user.list.table.columns.real_name'),
      minWidth: 130,
    },
    {
      field: 'avatar',
      title: $t('system.user.list.table.columns.avatar'),
      cellRender: {
        name: 'CellImage',
      },
      minWidth: 80,
      visible: false,
    },
    {
      field: 'managed_tenants',
      title: $t('system.user.list.table.columns.tenants'),
      minWidth: 170,
      slots: {
        default: ({ row }) => {
          //@ts-ignore
          const data = row?.managed_tenants || [];
          const getColorFromName = (name: any) => {
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
              hash += name.charCodeAt(i);
            }
            // 使用哈希值生成颜色
            const color = `hsl(${hash % 360}, 70%, 50%)`; // HSL颜色
            return color;
          };

          if (row.is_super == 1) {
           return  h(Tag, { key: 1, style: { backgroundColor: getColorFromName('ALL'), color: '#fff' } }, 'ALL')
          }


          return h('div', data.map((item: any) =>
            h(Tag, { key: item.id, style: { backgroundColor: getColorFromName(item?.company_name || ''), color: '#fff' } }, item?.company_name || '')
          ));
        },
      },
    },

    {
      field: 'mobile_phone',
      title: $t('system.user.list.table.columns.mobile_phone'),
      formatter({ cellValue }) {
        return cellValue || '暂无';
      },
      minWidth: 120,
    },
    {
      cellRender: {
        attrs: {
          beforeChange: onStatusChange,
          disabled: (row: User) => {
            return !!(row.is_super == 1);
          }
        },
        name: useAccess().hasAccessByCodes(['admin', 'system.user.status']) ? 'CellSwitch' : 'CellTag',

      },
      field: 'enabled',
      title: $t('system.user.list.table.columns.enabled'),
      width: 100,

    },
    {
      field: 'is_locked',
      title: $t('system.user.list.table.columns.is_locked'),
      minWidth: 150,
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_LOCKED_STATUS)
      },
    },
    {
      field: 'created_date',
      title: $t('system.user.list.table.columns.created_date'),
      minWidth: 150,
      visible: false,
    },
    {
      align: 'center',
      cellRender: {
        name: 'CellOperation', // 渲染器名称
        attrs: {
          nameField: 'name', // 自定义字段（如果需要）
          onClick: onActionClick,
        },
        options: [
          {
            code: 'edit',
            text: $t('system.user.list.table.columns.actions.edit'),
            auth: ['admin', 'platform:tenant_member:edit'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super);
            }
          },
          // {
          //   code: 'tenant_grant',
          //   text: $t('system.user.list.table.columns.actions.tenant_grant'),
          //   auth: ['admin', 'platform:tenant_member:edit'],
          //   show: (_values: any) => {
          //     //超级角色屏蔽
          //     return ![1].includes(_values.is_super);
          //   }
          // },
          {
            code: 'managed_tenants',
            text: $t('system.user.list.table.columns.actions.managed_tenants'),
            auth: ['admin', 'platform:tenant_member:managed_tenants'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super);
            }
          },
          {
            code: 'delete',
            text: $t('system.user.list.table.columns.actions.delete.label'),
            auth: ['admin', 'platform:tenant_member:delete'],
            show: (_values: any) => {
              //超级角色屏蔽
              return ![1].includes(_values.is_super);
            }
          },
          {
            code: 'locked',
            text: $t('system.user.list.table.columns.actions.locked.label'),
            auth: ['admin', 'platform:tenant_member:locked'],
            show: (_values: any) => {
              return ([0].includes(_values.is_locked) && ![1].includes(_values.is_super));
            }
          },
          {
            code: 'un_locked',
            text: $t('system.user.list.table.columns.actions.un_locked.label'),
            auth: ['admin', 'platform:tenant_member:un_locked'],
            show: (_values: any) => {
              return ([1].includes(_values.is_locked) && ![1].includes(_values.is_super));
            }
          },
          {
            code: 'reset_password',
            text: $t('system.user.list.table.columns.actions.reset_password'),
            auth: ['admin', 'platform:tenant_member:reset_password'],
            show: (_values: any) => {
              return ![1].includes(_values.is_super);
            }
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.user.list.table.columns.operation'),
      width: 200,
    }
  ];
}

/**
 * 定义表单数据
 * @returns 
 */
export function drawerSchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      dependencies: {
        show: () => false,
        triggerFields: [''],
      },
      fieldName: 'id',
    },
    {
      component: 'Input',
      fieldName: 'user_name',
      label: $t('system.user.form.modal.user_name'),
      componentProps: {
        placeholder: $t("system.user.form.placeholder.user_name"),
      },
      rules: 'required',
    },
    {
      component: 'InputPassword',
      fieldName: 'password',
      label: $t('system.user.form.modal.password'),
      componentProps: {
        placeholder: $t("system.user.form.placeholder.password"),
      },
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'real_name',
      label: $t('system.user.form.modal.real_name'),
      componentProps: {
        placeholder: $t("system.user.form.placeholder.real_name"),
      },
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'mobile_phone',
      label: $t('system.user.form.modal.mobile_phone'),
      defaultValue: undefined,
      componentProps: {
        placeholder: $t("system.user.form.placeholder.mobile_phone"),
      },
      rules: z
        .string()
        .regex(/^1[3-9]\d{9}$/, '请输入正确的手机号码')
        .optional()
        .or(z.literal('')),
    },
    {
      component: 'Input',
      fieldName: 'email',
      defaultValue: undefined,
      label: $t('system.user.form.modal.email'),
      componentProps: {
        placeholder: $t("system.user.form.placeholder.email"),
      },
      /**
       * z.literal 是 Zod 中的一种类型，用于定义一个特定的字面量值。
       * 它可以用于确保输入的值与指定的字面量完全匹配。
       * 例如，你可以使用 z.literal 来确保某个字段的值只能是特定的字符串、数字、布尔值等。
       * 即空字符串也可通过校验
       */
      rules: z.string().email($t("system.user.form.placeholder.email")).optional().or(z.literal('')),
    },
    {
      component: 'RadioGroup',
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_SEX),
        optionType: 'button',
      },
      defaultValue: 0,
      fieldName: 'sex',
      label: $t('system.user.form.modal.sex'),
    },
    {
      component: 'RadioGroup',
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      defaultValue: 0,
      fieldName: 'enabled',
      label: $t('system.user.form.modal.enabled'),
    },
    {
      component: 'Textarea',
      fieldName: 'remark',
      formItemClass: 'items-start',
      label: $t('system.user.form.modal.remark'),
      componentProps: {
        placeholder: $t("system.user.form.placeholder.remark"),
      }
    },
  ]
}
