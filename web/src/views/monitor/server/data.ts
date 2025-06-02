import type {VxeGridProps } from '#/adapter';
import { $t } from '#/locale';


// CPU信息定义
export const gridSchemas: VxeGridProps<any> = {
  toolbarConfig: {
    refresh: false, // 刷新
    print: false, // 打印
    export: false, // 导出
    zoom: false, // 最大化最小化
    enabled:false,

  },
  columnConfig: {
    resizable: false,//开启拖拽行宽模式
  },
  columns: [
    {
      field: 'key',
      title: $t('monitors.server.columns.key'),
      align:'left',
      width:170,
      cellRender: {
        name: 'ApiDict',
        props: {
          code: 'monitor_server_cpu',
        },
      },
    },
    { field: 'value', title: $t('monitors.server.columns.value'),align:'left' },
  ],
};


/**
 * 内存信息
 */
export const gridMemorySchemas: VxeGridProps<any> = {
  toolbarConfig: {
    refresh: false, // 刷新
    print: false, // 打印
    export: false, // 导出
    zoom: false, // 最大化最小化
    enabled:false,
  },
  columnConfig: {
    resizable: false,//开启拖拽行宽模式
  },
  columns: [
    {
      field: 'key',
      title:  $t('monitors.server.columns.key'),
      align:'left',
      width:170,
      cellRender: {
        name: 'ApiDict',
        props: {
          code: 'monitor_server_memory',
        },
      },
    },
    { field: 'value',  title:$t('monitors.server.columns.value'),align:'left' },
  ],
};


export const gridPhpSchemas: VxeGridProps<any> = {
  toolbarConfig: {
    refresh: false, // 刷新
    print: false, // 打印
    export: false, // 导出
    zoom: false, // 最大化最小化
    enabled:false,
  },
  columnConfig: {
    resizable: false,//开启拖拽行宽模式
  },
  columns: [
    {
      field: 'key',
      title: $t('monitors.server.columns.key'),
      align:'left',
      width:170,
      cellRender: {
        name: 'ApiDict',
        props: {
          code: 'monitor_server_memory',
        },
      },
    },
    { field: 'value', title: $t('monitors.server.columns.value'),align:'left' },
  ],
};


export const gridDiskSchemas: VxeGridProps<any> = {
  toolbarConfig: {
    refresh: false, // 刷新
    print: false, // 打印
    export: false, // 导出
    zoom: false, // 最大化最小化
    enabled:false,
  },
  columnConfig: {
    resizable: false,//开启拖拽行宽模式
  },
  columns: [
    { field: 'filesystem', title: $t('monitors.server.columns.filesystem'),align:'left' },
    { field: 'size', title: $t('monitors.server.columns.size'),align:'left' },
    { field: 'available', title: $t('monitors.server.columns.available'),align:'left' },
    { field: 'used', title: $t('monitors.server.columns.used'),align:'left' },
    { field: 'use_percentage', title: $t('monitors.server.columns.use_percentage'),align:'left' },
    { field: 'mounted_on', title: $t('monitors.server.columns.mounted_on'),align:'left' },
  ],
};
