import type { VxeTableGridOptions } from '#/components/common/effects/plugins/vxe-table';

import type { FormSchema } from '#/adapter/form';
import type { OnActionClickFn } from '#/adapter/vxe-table';
import { z } from '#/adapter/form';
import { type SystemDept, SystemDeptApi } from '#/api/system/dept';
import { $t } from '#/locale';
import { getPopupContainer } from '#/components/common/utils';
import { getDictOptions } from '#/utils/dict';
import { DictEnum } from '#/components/common/constants';
import { useAccess } from '#/components/common/effects/access';
import { h, RendererElement, RendererNode, VNode, VNodeArrayChildren } from 'vue';
import { Tag } from 'ant-design-vue';





const api = new SystemDeptApi();

/**
 * 获取编辑表单的字段配置。如果没有使用多语言，可以直接export一个数组常量
 */
export function useSchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'code',
      label: $t('system.dept.form.modal.code'),
      rules: z
        .string()
        .min(2, $t('ui.formRules.minLength', [$t('system.dept.form.modal.code'), 4]))
        .max(
          20,
          $t('ui.formRules.maxLength', [$t('system.dept.form.modal.code'), 20]),
        ),
      componentProps: {
        placeholder: $t('system.dept.form.modal.placeholder.code')
      }
    },
    {
      component: 'Input',
      fieldName: 'name',
      label: $t('system.dept.form.modal.name'),
      rules: z
        .string()
        .min(2, $t('ui.formRules.minLength', [$t('system.dept.form.modal.name'), 4]))
        .max(
          20,
          $t('ui.formRules.maxLength', [$t('system.dept.form.modal.name'), 20]),
        ),
      componentProps: {
        placeholder: $t('system.dept.form.modal.placeholder.name')
      }
    },
    {
      component: 'ApiTreeSelect',
      componentProps: {
        allowClear: true,
        api: () => {
          return api.list({ format: 'table_tree', page: 1, limit: 999 });
        },
        class: 'w-full',
        labelField: 'name',
        valueField: 'id',
        childrenField: 'children',
        placeholder: $t('system.dept.form.modal.placeholder.parent_dept')
      },
      fieldName: 'pid',
      label: $t('system.dept.form.modal.parent_dept')
    },
    {
      component: 'RadioGroup',
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      defaultValue: 1,
      fieldName: 'enabled',
      label: $t('system.dept.form.modal.enabled'),
    },
    {
      component: 'Textarea',
      componentProps: {
        maxLength: 50,
        rows: 3,
        showCount: true,
        class: 'w-full',
        placeholder: $t('system.dept.form.modal.placeholder.remark')
      },
      fieldName: 'remark',
      label: $t('system.dept.form.modal.remark'),
      rules: z
        .string()
        .max(50, $t('ui.formRules.maxLength', [$t('system.dept.remark'), 50]))
        .optional(),
    },
  ];
}

/**
 * 搜索表单
 * @returns 
 */
export function querySchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('system.dept.list.query.name'),
      componentProps: {
        placeholder: $t('system.dept.list.query.placeholder.name')
      }
    },
    {
      component: 'Select',
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_YES_NO),
        placeholder: $t('system.dept.list.query.placeholder.enabled')
      },
      fieldName: 'enabled',
      label: $t('system.dept.list.query.enabled'),
    },
  ];
}


/**
 * 获取表格列配置
 * @description 使用函数的形式返回列数据而不是直接export一个Array常量，是为了响应语言切换时重新翻译表头
 * @param onActionClick 表格操作按钮点击事件
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemDept>,
  onStatusChange?: (newStatus: any, row: SystemDept) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemDept>['columns'] {
  return [
    {
      align: 'left',
      field: 'name',
      fixed: 'left',
      title: $t('system.dept.list.table.columns.name'),
      treeNode: true,
      minWidth: 150,
    },
    {
      align: 'left',
      field: 'code',
      fixed: 'left',
      title: $t('system.dept.list.table.columns.code'),
      minWidth: 100,
    },
    {
      align: 'left',
      field: 'leader',
      fixed: 'left',
      title: $t('system.dept.list.table.columns.leader'),
      minWidth: 100,
      slots: {
        default: ({ row }) => {
          const matrix = row.leader || [];
          return matrix.map((item: { real_name: string | number | boolean | VNode<RendererNode, RendererElement, { [key: string]: any; }> | VNodeArrayChildren | (() => any) | { [name: string]: unknown; $stable?: boolean; } | undefined; }) =>
            h(
              Tag,
              { color: 'processing' }, // 添加key以避免渲染问题
              item.real_name
            )
          );
        },
      }
    },
    {
      cellRender: {
        attrs: {
          beforeChange: onStatusChange,
          disabled: (row: SystemDept) => {
            return !(row?.children?.length == 0|| row.children == undefined)
          }
        },
        name: useAccess().hasAccessByCodes(['admin', 'system.role.status']) ? 'CellSwitch' : 'CellTag',

      },
      field: 'enabled',
      title: $t('system.dept.list.table.columns.enabled'),
      width: 100,
    },

    {
      field: 'sort',
      title: $t('system.dept.list.table.columns.sort'),
      width: 100,
    },
    {
      field: 'created_date',
      resizable: false,
      title: $t('system.dept.list.table.columns.created_date'),
      width: 'auto',
    },
    {
      field: 'remark',
      title: $t('system.dept.list.table.columns.remark'),
      visible: false,
    },
    {
      align: 'right',
      cellRender: {
        attrs: {
          nameField: 'name',
          nameTitle: $t('system.dept.list.table.columns.name'),
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'append',
            text: $t('system.dept.list.table.columns.actions.append'),
            auth: ['admin', 'system:dept:create']
          },
          {
            code: 'edit',
            text: $t('system.dept.list.table.columns.actions.edit'),
            auth: ['admin', 'system:dept:edit']
          },
          {
            code: 'delete',
            text: $t('system.dept.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:dept:delete'],
            disabled: (row: SystemDept) => {
              return !!(row.children && row.children.length > 0);
            },
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.dept.list.table.columns.operation'),
      width: 200,
    },
  ];
}
