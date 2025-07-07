import type { FormProps, VxeGridProps } from '#/adapter';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';
// 表格参数定义
export const gridSchemas: VxeGridProps<any> = {
  toolbarConfig: {
    refresh: true, // 刷新
    print: false, // 打印
    export: false, // 导出
    // custom: true, // 自定义列
    zoom: true, // 最大化最小化
    search: true,
  },
  columns: [
    {
      type: 'checkbox',
      width: 60
    },
    {
      field: 'target',
      title: $t('dev.crontab_log.list.table.columns.target'),
      width: 170
    },
    {
      field: 'running_time',
      title: $t('dev.crontab_log.list.table.columns.running_time'),
    },

    {
      field: 'return_code',
      title: $t('dev.crontab_log.list.table.columns.return_code'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_OPERATION_RESULT)
      },
    },
    {
      field: 'log',
      title: $t('dev.crontab_log.list.table.columns.log'),
    },
    {
      field: 'created_date',
      minWidth:140,
      title: $t('dev.crontab_log.list.table.columns.created_date'),
    },
    {
      width: 120,
      title: $t('dev.crontab_log.list.table.columns.operation'),
      align: 'center',
      slots: { default: 'ACTION' },
      fixed: 'right',
    },
  ],
};
// 搜索表单参数定义
export const searchFormSchemas: FormProps = {
  schema: [
    {
      component: 'Input',
      fieldName: 'LIKE_target',
      label: $t('dev.crontab_log.list.query.target'),
      componentProps: {
        placeholder: $t('dev.crontab_log.list.query.placeholder.target'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'return_code',
      label: $t('dev.crontab_log.list.query.return_code'),
      componentProps: {
        placeholder: $t('dev.crontab_log.list.query.placeholder.return_code'),
        options: getDictOptions(DictEnum.SYS_OPERATION_RESULT),
        allowClear: true,
      },
    }
  ],
};
