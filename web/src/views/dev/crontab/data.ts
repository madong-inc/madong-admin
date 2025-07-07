import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import { SystemCrontabRow } from '#/api/dev/crontab';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';


/***
 * 定义表格
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemCrontabRow>,
  //@ts-ignore
  onStatusChange?: (newStatus: any, row: SystemCrontabRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemCrontabRow>['columns'] {
  return [
    // { type: 'checkbox', width: 60 },
    {
      field: 'title',
      title: $t('dev.crontab.list.table.columns.title'),
      align: 'left',
      minWidth:150
    },
    {
      field: 'rule',
      title: $t('dev.crontab.list.table.columns.rule'),
      align: 'left'
    },
    {
      field: 'singleton',
      title: $t('dev.crontab.list.table.columns.singleton'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_MONITOR_CRONTAB_MODE)
      },
    },
    {
      field: 'rule_name',
      align: 'left',
      title: $t('dev.crontab.list.table.columns.rule_name'),
    },
    {
      field: 'target',
      title: $t('dev.crontab.list.table.columns.target'),
      align: 'left',
      minWidth: 170
    },
    {
      field: 'enabled',
      title: $t('dev.crontab.list.table.columns.enabled'),
      cellRender: {
        name: 'CellTag',
        options: getDictOptions(DictEnum.SYS_YES_NO)
      },
    },
    {
      field: 'running_times',
      title: $t('dev.crontab.list.table.columns.running_times'),
      align: 'left'
    },
    {
      field: 'last_running_time',
      title: $t('dev.crontab.list.table.columns.last_running_time'),
      width: 170
    },
    {
      align: 'center',
      cellRender: {
        attrs: {
          nameField: 'title',
          onClick: onActionClick,
        },
        name: 'CellOperation',
        options: [
          {
            code: 'edit',
            text: $t('dev.crontab.list.table.columns.actions.edit'),
            auth: ['admin', 'dev:crontab:edit'],
          },
          {
            text: $t('dev.crontab.list.table.columns.actions.execute'),
            code: 'execute',
            auth: ['admin', 'dev:crontab:execute'],
          },
          {
            text: $t('dev.crontab.list.table.columns.actions.logs'),
            code: 'logs',
            auth: ['admin', 'dev:crontab:logs'],
          },
          {
            text: $t('dev.crontab.list.table.columns.actions.resume'),
            code: 'resume',
            auth: ['admin', 'dev:crontab:resume'],
            show: (row: SystemCrontabRow) => {
              return (row.enabled === 0);
            }
          },
          {
            text: $t('dev.crontab.list.table.columns.actions.pause'),
            code: 'pause',
            auth: ['admin', 'dev:crontab:pause'],
            show: (row: SystemCrontabRow) => {
              return (row.enabled === 1);
            }
          },
          {
            code: 'delete',
            auth: ['admin', 'dev:crontab:delete'],
          },
        ],
      },
      field: 'operation',
      fixed: 'right',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('dev.crontab.list.table.columns.operation'),
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
      fieldName: 'LIKE_title',
      label: $t('dev.crontab.list.query.title'),
      componentProps: {
        placeholder: $t('dev.crontab.list.query.placeholder.title'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'type',
      label: $t('dev.crontab.list.query.type'),
      componentProps: {
        placeholder: $t('dev.crontab.list.query.placeholder.type'),
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_MONITOR_CRONTAB_TYPE),
      },
    },
    {
      component: 'Select',
      fieldName: 'enabled',
      label: $t('dev.crontab.list.query.enabled'),
      componentProps: {
        placeholder: $t('dev.crontab.list.query.placeholder.enabled'),
        options: getDictOptions(DictEnum.SYS_YES_NO),
        allowClear: true,
      },
    }
  ];
}


/**
 * 表单定义
 * @returns 
 */
export function formSchamas(): FormSchema[] {
  return [
    {
      fieldName: 'id',
      label: $t('dev.crontab.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'title',
      label: $t('dev.crontab.form.modal.title'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.crontab.form.modal.placeholder.title'),
        allowClear: true,
        class: ''
      },
      rules: 'required',
    },
    {
      fieldName: 'type',
      label: $t('dev.crontab.form.modal.type'),
      component: 'Select',
      defaultValue: 1,
      componentProps: {
        placeholder: $t('dev.crontab.form.modal.type'),
        options: getDictOptions(DictEnum.SYS_MONITOR_CRONTAB_TYPE)
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'rule',
      label: $t('dev.crontab.form.modal.rule'),
      component: 'Input',
      componentProps: {
        placeholder: $t('dev.crontab.form.modal.placeholder.rule'),
        allowClear: true,
      },
      dependencies: {
        triggerFields: ['id'],
        show: (values) => {
          //使用后端转换rule模式
          return false;
          // return values.id !== undefined && values.id !== '' && values.id !== null;
        },
        disabled: (values) => {
          return values.id !== undefined && values.id !== '' && values.id !== null;
        },
      }
    },
    {
      fieldName: 'task_cycle',
      label: $t('dev.crontab.form.modal.task_cycle'),
      component: 'Select',
      defaultValue: 1,
      componentProps: {
        placeholder: $t('dev.crontab.form.modal.placeholder.task_cycle'),
        options: getDictOptions(DictEnum.SYS_MONITOR_CRONTAB_CYCLE),
      },
      formItemClass: 'col-span-4',
      rules: 'selectRequired',
    },
    {
      fieldName: 'month',
      label: '',
      labelWidth: 0,
      suffix: () => $t('dev.crontab.form.modal.suffix.month'),//月
      component: 'InputNumber',
      formItemClass: 'col-span-2',
      dependencies: {
        triggerFields: ['task_cycle'],
        show: (values) => {
          //年份月份的时候显示月份输入
          return [5].includes(values.task_cycle);
        },
      },
      componentProps: {
        min: 1,
        max: 12
      }
    },
    {
      fieldName: 'day',
      label: '',
      labelWidth: 0,
      suffix: () => $t('dev.crontab.form.modal.suffix.day'),//日
      component: 'InputNumber',
      formItemClass: 'col-span-2',
      dependencies: {
        triggerFields: ['task_cycle'],
        show: (values) => {
          //年份月份的时候显示
          return [4, 5].includes(values.task_cycle);
        },
      },
      componentProps: {
        min: 1,
        max: 31
      }
    },
    {
      fieldName: 'week',
      label: '',
      labelWidth: 1,
      component: 'Select',
      dependencies: {
        triggerFields: ['task_cycle'],
        show: (values) => {
          //每星期的时候显示周选项
          return [3].includes(values.task_cycle);
        },
      },
      componentProps: {
        allowClear: true,
        filterOption: true,
        options: getDictOptions(DictEnum.SYS_MONITOR_CRONTAB_WEEK),
        placeholder: '',
        showSearch: true,
      },
      formItemClass: 'col-span-2',
    },
    {
      fieldName: 'hour',
      label: '',
      labelWidth: 0,
      suffix: () => $t('dev.crontab.form.modal.suffix.hour'),//时
      component: 'InputNumber',
      formItemClass: 'col-span-2',
      dependencies: {
        triggerFields: ['task_cycle'],
        show: (values) => {
          //每星期的时候显示周选项
          return [1, 3, 4, 6, 5].includes(values.task_cycle);
        },
      },
      componentProps: {
        min: 1,
        max: 23
      }
    },
    {
      fieldName: 'minute',
      label: '',
      labelWidth: 0,
      suffix: () => $t('dev.crontab.form.modal.suffix.minute'),//分
      component: 'InputNumber',
      formItemClass: 'col-span-2',
      dependencies: {
        triggerFields: ['task_cycle'],
        show: (values) => {
          return [1, 2, 3, 4, 6, 7, 5].includes(values.task_cycle);
        },
      },
      componentProps: {
        min: 0,
        max: 59
      }
    },
    {
      fieldName: 'second',
      label: '',
      labelWidth: 0,
      suffix: () => $t('dev.crontab.form.modal.suffix.second'),//秒
      component: 'InputNumber',
      formItemClass: 'col-span-2',
      dependencies: {
        triggerFields: ['task_cycle'],
        show: (values) => {
          return [8].includes(values.task_cycle);
        },
      },
      componentProps: {
        min: 0,
        max: 59
      }
    },
    {
      fieldName: 'target',
      // label: '调用目标',
      label: $t('dev.crontab.form.modal.target'),
      component: 'Textarea',
      componentProps: {
        placeholder: $t('dev.crontab.form.modal.target'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'singleton',
      label: $t('dev.crontab.form.modal.singleton'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_MONITOR_CRONTAB_MODE),
        optionType: 'button',
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'enabled',
      label: $t('dev.crontab.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO),
        optionType: 'button',
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'remark',
      label: $t('dev.crontab.form.modal.remark'),
      component: 'Textarea',
      componentProps: {
        placeholder: $t('dev.crontab.form.modal.placeholder.remark')
      }
    },
  ];
}
