import type { VxeGridProps } from '#/adapter/vxe-table';
import type { SystemMenuRow } from '#/api/system/menu';

import { h, markRaw } from 'vue';

import {IconifyIcon} from '#/components/common/icons';

export interface Permission {
  checked: boolean;
  id: string | number;
  title: string;
}

export interface MenuPermissionOption extends SystemMenuRow {
  permissions: Permission[];
}

const menuTypes = {
  // 1: { icon: markRaw(FolderIcon), value: '目录' },
  // 2: { icon: markRaw(MenuIcon), value: '菜单' },
  // 3: { icon: markRaw(OkButtonIcon), value: '按钮' },
  // 4: { icon: markRaw(FolderIcon), value: 'API' },
  1: { icon: '', value: '目录' },
  2: { icon: '', value: '菜单' },
  3: { icon: '', value: '按钮' },
  4: { icon: '', value: 'API' },
}
export const nodeOptions = [
  { label: '节点关联', value: true },
  { label: '节点独立', value: false },
];

export const columns: VxeGridProps['columns'] = [
  {
    type: 'checkbox',
    title: '菜单名称',
    field: 'title',
    treeNode: true,
    headerAlign: 'left',
    align: 'left',
    width: 230,
  },
  {
    title: '图标',
    field: 'icon',
    width: 80,
    slots: {
      default: ({ row }) => {
        if (row?.icon === '#') {
          return '';
        }
        return h('span', { class: 'flex justify-center' }, [
          h(IconifyIcon, { icon: row.icon }),
        ]);
      },
    },
  },
  {
    title: '类型',
    field: 'type',
    width: 80,
    slots: {
      default: ({ row }) => {
        const current = menuTypes[row.type as 1 | 2 | 3| 4];
        if (!current) {
          return '未知';
        }
        return h('span', { class: 'flex items-center justify-center gap-1' }, [
          h(current.icon, { class: 'size-[18px]' }),
          h('span', current.value),
        ]);
      },
    },
  },
  {
    title: '权限接口',
    field: 'permissions',
    headerAlign: 'left',
    align: 'left',
    slots: {
      default: 'permissions',
    },
  },
];
