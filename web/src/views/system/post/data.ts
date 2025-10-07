
import type { FormSchema } from '#/adapter/form';
import type { OnActionClickFn, VxeTableGridOptions } from '#/adapter/vxe-table';
import { SystemDeptApi } from '#/api/system/dept';
import type{ SystemPostRow } from '#/api/system/post';

import { DictEnum } from '#/components/core/constants';
import { Recordable } from '#/components/core/types';
import { getPopupContainer } from '#/components/core/utils';
import { Icon } from '#/components/icon';
import { $t } from '#/locale';



import { getDictOptions } from '#/utils/dict';
import { renderDict } from '#/utils/render';
import { h } from 'vue';


const api=new SystemDeptApi();

/**
 * 定义搜索表单
 * @returns 
 */
export function querySchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_code',
      label: $t('system.post.list.query.code'),  // 确保 $t 函数返回字符串类型 
      componentProps:{
        placeholder:$t('system.post.list.query.placeholder.code')
      }
    },
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('system.post.list.query.name'), 
      componentProps:{
        placeholder:$t('system.post.list.query.placeholder.name')
      }
    },
    {
      component: 'Select',
      componentProps: {
        getPopupContainer: getPopupContainer, // 需确保该函数已正确定义 
        options: getDictOptions(DictEnum.SYS_YES_NO), // 验证字典获取方法 
        placeholder:$t('system.post.list.query.placeholder.enabled')
      } as const, // 使用 const 断言锁定类型 
      fieldName: 'enabled',
      label: $t('system.post.list.query.enabled'), 
    },
  ];
}


/**
 * 定义表格字段数据
 * @param onActionClick 
 * @returns 
 */
export function useColumns(onActionClick?: OnActionClickFn<SystemPostRow>): VxeTableGridOptions<SystemPostRow>['columns'] {
  return [
    { type: 'checkbox', width: 60 },
    {
      title:  $t('system.post.list.table.columns.code'),
      field: 'code',
    },
    {
      title: $t('system.post.list.table.columns.dept_name'),
      field: 'dept_id',
      visible:false
    },
    {
      title:  $t('system.post.list.table.columns.name'),
      field: 'name',
    },
    {
      title:  $t('system.post.list.table.columns.sort'),
      field: 'sort',
    },
    {
      title:  $t('system.post.list.table.columns.enabled'),
      field: 'enabled',
      slots: {
        default: ({ row }) => {
          return renderDict(row.enabled, DictEnum.SYS_YES_NO);
        },
      },
    },
    {
      title: $t('system.post.list.table.columns.created_date'),
      field: 'created_date',
      minWidth:150
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
            text: $t('system.post.list.table.columns.actions.edit'),
            auth: ['admin', 'system:post:edit'],
          },
          {
            code: 'delete',
            text: $t('system.post.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:post:delete'],
          }
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.post.list.table.columns.operation'),
      width: 250,
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
      label:$t('system.post.form.modal.id')
    },
    {
      component: 'ApiTreeSelect',
      componentProps: {
        api: () => {
          return api.list({ format: 'tree'});
        },
        class: 'w-full',
        filterTreeNode(input: string, node: Recordable<any>) {
          if (!input || input.length === 0) {
            return true;
          }
          const name: string = node.label ?? '';
          if (!name) return false;
          return name.includes(input) || $t(name).includes(input);
        },
        getPopupContainer,
        labelField: 'name',
        showSearch: true,
        treeDefaultExpandAll: true,
        valueField: 'id',
        childrenField: 'children',
        alwaysLoad: false,
        placeholder:$t('system.post.form.modal.placeholder.dept_name')
      },
      fieldName: 'dept_id',
      label:$t('system.post.form.modal.dept_name'),
      renderComponentContent() {
        return {
          title({ label, icon }: { label: string; icon: string }) {
            const coms = [];
            if (!label) return '';
            if (icon && icon !== '') {
              coms.push(h(Icon, { class: 'size-4', icon: icon }));
            }
            coms.push(h('span', { class: '' }, $t(label || '')));
            return h('div', { class: 'flex items-center gap-1' }, coms);
          },
        };
      },
      rules: 'selectRequired',
    },


    {
      component: 'Input',
      fieldName: 'name',
      label:$t('system.post.form.modal.name'),
      rules: 'required',
      componentProps:{
        placeholder:$t('system.post.form.modal.placeholder.name')
      }
    },
    {
      component: 'Input',
      fieldName: 'code',
      label:$t('system.post.form.modal.code'),
      rules: 'required',
      componentProps:{
        placeholder:$t('system.post.form.modal.placeholder.code')
      }
    },
    {
      component: 'InputNumber',
      fieldName: 'sort',
      defaultValue:0,
      label:$t('system.post.form.modal.sort'),
      rules: 'required',
      componentProps:{
        placeholder:$t('system.post.form.modal.placeholder.sort')
      }
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
      label:$t('system.post.form.modal.enabled'),
      rules: 'required',
    },
    {
      component: 'Textarea',
      fieldName: 'remark',
      formItemClass: 'items-start',
      label:$t('system.post.form.modal.remark'),
      componentProps:{
        placeholder:$t('system.post.form.modal.placeholder.remark')
      }
    },
  ]
}
