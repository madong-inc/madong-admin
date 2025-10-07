import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type{ SystemDictRow } from '#/api/system/dict';
import { DictEnum } from '#/components/core/constants';
import { useAccess } from '#/components/core/effects/access';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils/dict';



/**
 * 定义表格字段数据
 * @param onActionClick 
 * @returns 
 */
export function useColumns(
  onActionClick?: OnActionClickFn<SystemDictRow>,
  onStatusChange?: (newStatus: any, row: SystemDictRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemDictRow>['columns'] {
  return [
      { type: 'checkbox', width: 60 },
      {
        field: 'group_code',
        title: $t('system.dict.list.table.columns.group_code'),
        align:'left',
        cellRender: {
          name: 'CellTag',
          options:getDictOptions(DictEnum.SYS_DICT_GROUP_CODE),
        },
        visible:false
      },
      { 
        field: 'name',
         title: $t('system.dict.list.table.columns.name'),
         align:'left' 
        },
      { 
        field: 'code',
         title: $t('system.dict.list.table.columns.code'),
         align:'left' 
        },
      { 
        field: 'sort', 
        title: $t('system.dict.list.table.columns.sort'),
      },
      {
        field: 'data_type',
        cellRender: {
          name: 'CellTag',
          options:getDictOptions(DictEnum.SYS_DICT_DATA_TYPE)
        },
        title: $t('system.dict.list.table.columns.data_type'),
      },
      {
        cellRender: {
          attrs: { 
            beforeChange: onStatusChange,
            disabled:(row:SystemDictRow)=>{
              return !!(row.group_code == 'default');
            }
          },
          name: useAccess().hasAccessByCodes(['admin','system.dict.list.table.columns.enabled']) ? 'CellSwitch' : 'CellTag',
          
        },
        field: 'enabled',
        title: $t('system.dict.list.table.columns.enabled'),
        width: 100,
      },
      {
        align: 'center',
        cellRender: {
          name: 'CellOperation',
          attrs: {
            nameField: 'name',
            onClick: onActionClick,
          },
          options: [
            {
              code: 'dict_item',
              text: $t('system.dict.list.table.columns.actions.item'),
              auth: ['admin', 'system:dict_item:list'],
            },
            {
              code: 'edit',
              text: $t('system.dict.list.table.columns.actions.edit'),
              auth: ['admin', 'system:dict:edit'],
            },
            {
              code: 'delete',
              text: $t('system.dict.list.table.columns.actions.delete.label'),
              auth: ['admin', 'system:dict:delete'],
            }
          ],
        },
        field: 'operation',
        fixed: 'right',
        headerAlign: 'center',
        showOverflow: false,
        title: $t('system.dict.list.table.columns.operation'),
        width: 180,
      }
  ]
}


// 搜索表单参数定义
export  function querySchema(): FormSchema[]  {
  return [
    {
      component: 'Select',
      fieldName: 'IN_group_code',
      label: $t('system.dict.list.query.group_code'),
      componentProps: {
        placeholder: $t('system.dict.list.query.placeholder.group_code'),
        allowClear: true,
        options:getDictOptions('sys_dict_group_code'),
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_name',
      label: $t('system.dict.list.query.name'),
      componentProps: {
        placeholder: $t('system.dict.list.query.placeholder.name'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_code',
      label: $t('system.dict.list.query.code'),
      componentProps: {
        placeholder: $t('system.dict.list.query.placeholder.code'),
        allowClear: true,
      },
    },
    {
      component: 'Select',
      fieldName: 'EQ_enabled',
      label: $t('system.dict.list.query.enabled'),
      componentProps: {
        placeholder: $t('system.dict.list.query.placeholder.enabled'),
        allowClear: true,
        options:getDictOptions(DictEnum.SYS_YES_NO)
       },
    },
  ]
};


// 表单参数定义
export function formSchamas(): FormSchema[] {
return [
    {
      fieldName: 'id',
      label: $t('system.dict.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'group_code',
      label: $t('system.dict.form.modal.group_code'),
      component: 'Select',
      defaultValue: 'default',
      componentProps: {
        allowClear: true,
        placeholder: $t('system.dict.form.modal.placeholder.group_code'),
        options:getDictOptions(DictEnum.SYS_DICT_GROUP_CODE),
        class:'w-full'
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'name',
      label: $t('system.dict.form.modal.name'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.dict.form.modal.placeholder.name'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'code',
      label: $t('system.dict.form.modal.code'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.dict.form.modal.placeholder.code'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'data_type',
      label: $t('system.dict.form.modal.data_type'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        optionType: 'button',
        buttonStyle: 'solid',
        options:getDictOptions(DictEnum.SYS_DICT_DATA_TYPE)
      },
      rules: 'selectRequired',
    },
    {
      fieldName: 'enabled',
      label: $t('system.dict.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        optionType: 'button',
        buttonStyle: 'solid',
        options:getDictOptions(DictEnum.SYS_YES_NO)
      }
    },
    {
      fieldName: 'sort',
      label: $t('system.dict.form.modal.sort'),
      component: 'InputNumber',
      componentProps: {
        placeholder: $t('system.dict.form.modal.placeholder.sort'),
        allowClear: true,
        class:'w-full'
      },
    },
    {
      fieldName: 'remark',
      label: $t('system.dict.form.modal.remark'),
      component: 'Textarea',
      componentProps: {
        placeholder: $t('system.dict.form.modal.placeholder.remark'),
      },
    },
  ]
};
