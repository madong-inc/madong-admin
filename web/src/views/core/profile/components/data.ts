import type { FormSchemaGetter } from '#/adapter/form';
import type { VxeGridProps } from '#/adapter/vxe-table';
import { $t } from '#/locale';

import dayjs from 'dayjs';



export const querySchema: FormSchemaGetter = () => [
  {
    component: 'Input',
    fieldName: 'ipaddr',
    label: 'IP地址',
  },
  {
    component: 'Input',
    fieldName: 'user_name',
    label: '用户账号',
  },
];

export const columns: VxeGridProps['columns'] = [
  {
    title: $t('system.user.profile.online_device.table.columns.user_name'),
    field: 'user_name',
  },
  {
    title: $t('system.user.profile.online_device.table.columns.ip'),
    field: 'ip',
  },
  {
    title: $t('system.user.profile.online_device.table.columns.ip_location'),
    field: 'ip_location',
  },
  {
    title: $t('system.user.profile.online_device.table.columns.browser'),
    field: 'browser'
  },
  {
    title: $t('system.user.profile.online_device.table.columns.os'),
    field: 'os',
  },
  {
    title: $t('system.user.profile.online_device.table.columns.login_time'),
    field: 'login_time',
    width: 170,
    formatter: ({ cellValue }) => {
      return dayjs(cellValue * 1000).format('YYYY-MM-DD HH:mm:ss');
    },
  },
  {
    field: 'action',
    fixed: 'right',
    slots: { default: 'action' },
    title: $t('system.user.profile.online_device.table.columns.operation'),
    resizable: false,
    width: 'auto',
  },
];
