import type { FormProps, FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import { SystemMenuApi } from '#/api/system/menu';
import type { SystemMenuRow } from '#/api/system/menu';
import { DictEnum } from '#/components/core/constants';
import { IconifyIcon } from '#/components/core/icons';
import { Recordable } from '#/components/core/types';
import { getPopupContainer } from '#/components/core/utils';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils/dict';
import { h } from 'vue';

/**
 * 数据接口
 */
const systemMenuApi = new SystemMenuApi();



/**
 * 表格定义
 * @param onActionClick 
 * @returns 
 */
export function useColumns(onActionClick: OnActionClickFn<SystemMenuRow>): VxeTableGridOptions<SystemMenuRow>['columns'] {
  return [
    {
      align: 'left',
      field: 'title',
      fixed: 'left',
      slots: { default: 'title' },
      title: $t('system.menu.list.table.columns.title'),
      treeNode: true,
      width: 240,
    },
    {
      align: 'center',
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_MENU_TYPE)
      },
      field: 'type',
      title: $t('system.menu.list.table.columns.type'),
      width: 90,
    },
    {
      align: 'left',
      field: 'code',
      title: $t('system.menu.list.table.columns.code'),
      width: 200,
    },
    {
      align: 'left',
      field: 'path',
      title: $t('system.menu.list.table.columns.path'),
      width: 170,
    },

    {
      align: 'left',
      field: 'component',
      formatter: ({ row }) => {
        switch (row.type) {
          case 1:
          case 2:
          case 3:
          case 4:
            return row.component ?? '';
          case 5:
            return '';
          case 6:
            return row.link_url ?? '';
          default:
            console.warn(`Unknown row.type: ${row.type}`); // 可选：添加日志
            return ''; // 明确返回空字符串
        }
      },
      minWidth: 200,
      title: $t('system.menu.list.table.columns.component'),
    },
    {
      cellRender: { name: 'CellTag' },
      field: 'enabled',
      title: $t('system.menu.list.table.columns.enabled'),
      width: 100,
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
            code: 'save',
            text: $t('system.menu.list.table.columns.actions.api'),
            auth: ['admin', 'system:menu:create'],
          },
          {
            code: 'append',
            text: $t('system.menu.list.table.columns.actions.append'),
            auth: ['admin', 'system:menu:create'],
          },
          {
            code: 'edit',
            text: $t('system.menu.list.table.columns.actions.edit'),
            auth: ['admin', 'system:menu:edit'],
          },
          {
            code: 'delete',
            text: $t('system.menu.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:menu:delete'],
          }
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.menu.list.table.columns.operation'),
      width: 250,
    }
  ];
}



/**
 * 搜索表单
 * @returns 
 */
export function querySchema(): FormSchema[] {
  return [
    {
      component: 'Select',
      fieldName: 'type',
      label: $t('system.menu.list.query.type'),
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_MENU_TYPE),
        placeholder:$t('system.menu.list.query.placeholder.type'),
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_title',
      label: $t('system.menu.list.query.title'),
      componentProps:{
         placeholder:$t('system.menu.list.query.placeholder.title'),
      }
    },
    {
      component: 'Input',
      fieldName: 'LIKE_code',
      label: $t('system.menu.list.query.code'),
      componentProps:{
         placeholder:$t('system.menu.list.query.placeholder.code'),
      }
    },
    {
      component: 'Input',
      fieldName: 'LIKE_path',
      label: $t('system.menu.list.query.path'),
      componentProps:{
         placeholder:$t('system.menu.list.query.placeholder.path'),
      }
    },
    {
      component: 'Select',
      componentProps: {
        getPopupContainer,
        options: getDictOptions(DictEnum.SYS_YES_NO),
        placeholder:$t('system.menu.list.query.placeholder.enabled'),
      },
      fieldName: 'enabled',
      label: $t('system.menu.list.query.enabled'),
    },
  ];
}

/**
 * 表单定义
 */
export const formSchamas: FormProps = {
  commonConfig: {
    componentProps: {
      class: 'w-full',
    },
    formItemClass: 'col-span-2 md:col-span-2',
    labelWidth: 120,
  },
  wrapperClass: 'grid-cols-2 gap-x-4',
  showDefaultActions: false,
  schema: [
    {
      fieldName: 'id',
      label: $t('system.menu.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'type',
      label: $t('system.menu.form.modal.type'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_MENU_TYPE),
        optionType: 'button',
      },
      rules: 'selectRequired',
    },
    {
      component: 'ApiTreeSelect',
      defaultValue: 0,
      componentProps: {
        allowClear: true,
        api: () => {
          return systemMenuApi.list({ format: 'table_tree', page: 1, limit: 999 });
        },
        class: 'w-full',
        filterTreeNode(input: string, node: Recordable<any>) {
          if (!input || input.length === 0) {
            return true;
          }
          const title: string = node.label ?? '';
          if (!title) return false;
          return title.includes(input) || $t(title).includes(input);
        },
        getPopupContainer,
        labelField: 'title',
        showSearch: true,
        treeDefaultExpandAll: true,
        valueField: 'id',
        childrenField: 'children',
        alwaysLoad: false,
      },
      fieldName: 'pid',
      label: $t('system.menu.form.modal.pid'),
      renderComponentContent() {
        return {
          title({ label, icon }: { label: string; icon: string }) {
            const coms = [];
            if (!label) return '';
            if (icon && icon !== '') {
              coms.push(h(IconifyIcon, { class: 'size-4', icon: icon }));
            }
            coms.push(h('span', { class: '' }, $t(label || '')));
            return h('div', { class: 'flex items-center gap-1' }, coms);
          },
        };
      },
    },
    {
      fieldName: 'title',
      label: $t('system.menu.form.modal.title'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.title'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'code',
      label: $t('system.menu.form.modal.code'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.code'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'methods',
      label: $t('system.menu.form.modal.methods'),
      component: 'Select',
      defaultValue: 'GET',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.methods'),
        options: getDictOptions(DictEnum.SYS_REQUEST_MODE),
      },
      dependencies: {
        triggerFields: ['type'],
        show: (values) => {
          // 请求类型只有接口才需配置
          return [4].includes(values.type);
        },
      },
    },
    {
      fieldName: 'path',
       label: $t('system.menu.form.modal.path'),
      help: 'route.path',
      component: 'Input',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.path'),
        allowClear: true,
      },
      dependencies: {
        triggerFields: ['type'],
        show: (values) => {
          // 组件地址只有目录、菜单才需配置
          return [1, 2, 4].includes(values.type);
        },
      },
      rules: 'required',
    },
    {
      fieldName: 'component',
      label: $t('system.menu.form.modal.component'),
      help: 'route.component',
      component: 'Input',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.component'),
        allowClear: true,
      },
      dependencies: {
        triggerFields: ['type'],
        show: (values) => {
          // 组件地址只有菜单才需配置
          return [2].includes(values.type);
        },
      },
      rules: 'required',
    },
    {
      fieldName: 'open_type',
      label: $t('system.menu.form.modal.open_type'),
      component: 'RadioGroup',
      defaultValue: 0,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_MENU_OPEN_TYPE),
        optionType: 'button',
      },
      dependencies: {
        triggerFields: ['type'],
        show: (values) => {
          return [2].includes(values.type);
        },
      },
    },
    {
      fieldName: 'icon',
       label: $t('system.menu.form.modal.icon'),
      component: 'IconPicker',
      componentProps: {
        placeholder:$t('system.menu.form.modal.placeholder.icon'),
        allowClear: true,
      },
      dependencies: {
        triggerFields: ['type'],
        disabled: (values) => {
          // 组件地址只有目录、菜单才需配置
          return ![1, 2].includes(values.type);
        },
      },
    },
    {
      fieldName: 'link_url',
       label: $t('system.menu.form.modal.link_url'),
      help: 'url',
      component: 'Input',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.link_url'),
        allowClear: true,
      },
      dependencies: {
        triggerFields: ['open_type', 'type'],
        show: (values) => {
          // 只有目录、菜单才需配置
          return [2, 3].includes(values.open_type);
        },
      },
    },
    {
      fieldName: 'sort',
      label: $t('system.menu.form.modal.sort'),
      component: 'InputNumber',
      componentProps: {
        placeholder: $t('system.menu.form.modal.placeholder.sort'),
        allowClear: true,
      },
    },
    {
      fieldName: 'enabled',
      label: $t('system.menu.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
    },
    {
      fieldName: 'is_show',
      label: $t('system.menu.form.modal.is_show'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      dependencies: {
        triggerFields: ['type'],
        disabled: (values) => {
          // 只有目录、菜单才需配置
          return ![1, 2].includes(values.type);
        },
      },
    },
    {
      fieldName: 'is_cache',
      label: $t('system.menu.form.modal.is_cache'),
      component: 'RadioGroup',
      defaultValue: 0,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      dependencies: {
        triggerFields: ['type'],
        disabled: (values) => {
          // 组件地址只有目录、菜单才需配置
          return ![1, 2].includes(values.type);
        },
      },
    },
  ],
};